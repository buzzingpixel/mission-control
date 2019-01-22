<?php
declare(strict_types=1);

namespace src\app\projects\services;

use Cocur\Slugify\Slugify;
use src\app\data\Project\Project;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as DbFactory;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Project\ProjectRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\projects\events\ProjectAfterSaveEvent;
use src\app\projects\events\ProjectBeforeSaveEvent;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\exceptions\InvalidProjectModelException;
use src\app\projects\exceptions\ProjectNameNotUniqueException;

class SaveProjectService
{
    private $slugify;
    private $ormFactory;
    private $buildQuery;
    private $eventDispatcher;
    private $dbFactory;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        DbFactory $dbFactory
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
        $this->dbFactory = $dbFactory;
    }

    /**
     * @throws InvalidProjectModelException
     * @throws ProjectNameNotUniqueException
     */
    public function __invoke(ProjectModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidProjectModelException
     * @throws ProjectNameNotUniqueException
     */
    public function save(ProjectModelInterface $model): void
    {
        if (! $model->title()) {
            throw new InvalidProjectModelException();
        }

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->dbFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(Project::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new ProjectNameNotUniqueException();
        }

        $fetchModel = $this->dbFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(Project::class, $fetchModel)->fetchRecord();

        if (! $existingRecord) {
            $beforeEvent = new ProjectBeforeSaveEvent($model, true);

            $this->eventDispatcher->dispatch(
                $beforeEvent->provider(),
                $beforeEvent->name(),
                $beforeEvent
            );

            $this->saveNewProject($model);

            $afterEvent = new ProjectAfterSaveEvent($model, true);

            $this->eventDispatcher->dispatch(
                $afterEvent->provider(),
                $afterEvent->name(),
                $afterEvent
            );

            return;
        }

        $beforeEvent = new ProjectBeforeSaveEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $this->saveExistingProject($model, $existingRecord);

        $afterEvent = new ProjectAfterSaveEvent($model);

        $this->eventDispatcher->dispatch(
            $afterEvent->provider(),
            $afterEvent->name(),
            $afterEvent
        );
    }

    private function saveNewProject(ProjectModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Project::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function saveExistingProject(
        ProjectModelInterface $model,
        ProjectRecord $record
    ): void {
        $fetchModel = $this->dbFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());

        $this->finalSave($model, $record);
    }

    private function finalSave(ProjectModelInterface $model, ProjectRecord $record): void
    {
        $record->is_active = $model->isActive();
        $record->title = $model->title();
        $record->slug = $model->slug();
        $record->description = $model->description();
        $record->added_at = $model->addedAt()->format('Y-m-d H:i:s');
        $record->added_at_time_zone = $model->addedAt()->getTimezone()->getName();

        $this->ormFactory->makeOrm()->persist($record);
    }
}
