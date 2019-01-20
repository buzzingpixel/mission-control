<?php
declare(strict_types=1);

namespace src\app\projects\services;

use Cocur\Slugify\Slugify;
use Ramsey\Uuid\UuidFactory;
use src\app\data\Project\Project;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Project\ProjectRecord;
use src\app\datasupport\BuildQueryInterface;
use src\app\datasupport\FetchDataParamsFactory;
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
    private $uuidFactory;
    private $eventDispatcher;
    private $fetchDataParamsFactory;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        UuidFactory $uuidFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        FetchDataParamsFactory $fetchDataParamsFactory
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->uuidFactory = $uuidFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->fetchDataParamsFactory = $fetchDataParamsFactory;
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

        $fetchModel = $this->fetchDataParamsFactory->make();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->guid(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(Project::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new ProjectNameNotUniqueException();
        }

        if (! $model->guid()) {
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

        $this->saveExistingProject($model);

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
        $record->guid = $this->uuidFactory->uuid4()->toString();

        $this->finalSave($model, $record);
    }

    private function saveExistingProject(ProjectModelInterface $model): void
    {
        $fetchModel = $this->fetchDataParamsFactory->make();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->guid());

        $this->finalSave(
            $model,
            $this->buildQuery->build(Project::class, $fetchModel)->fetchRecord()
        );
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
