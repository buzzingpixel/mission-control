<?php
declare(strict_types=1);

namespace src\app\projects\services;

use DateTime;
use DateTimeZone;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\UuidFactory;
use src\app\data\Project\Project;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Project\ProjectRecord;
use src\app\datasupport\BuildQueryInterface;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\exceptions\InvalidProjectModelException;
use src\app\projects\exceptions\ProjectNameNotUniqueException;

class SaveProjectService
{
    private $slugify;
    private $ormFactory;
    private $buildQuery;
    private $uuidFactory;
    private $fetchDataParamsFactory;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        UuidFactory $uuidFactory,
        BuildQueryInterface $buildQuery,
        FetchDataParamsFactory $fetchDataParamsFactory
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->uuidFactory = $uuidFactory;
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
    public function save(ProjectModelInterface $model)
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
            $this->saveNewProject($model);
            return;
        }

        $this->saveExistingProject($model);
    }

    private function saveNewProject(ProjectModelInterface $model)
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Project::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->guid = $this->uuidFactory->uuid4()->toString();

        $this->finalSave($model, $record);
    }

    private function saveExistingProject(ProjectModelInterface $model)
    {
        $fetchModel = $this->fetchDataParamsFactory->make();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->guid());

        $this->finalSave(
            $model,
            $this->buildQuery->build(Project::class, $fetchModel)->fetchRecord()
        );
    }

    private function finalSave(ProjectModelInterface $model, ProjectRecord $record)
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
