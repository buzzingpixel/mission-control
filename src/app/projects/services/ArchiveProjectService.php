<?php
declare(strict_types=1);

namespace src\app\projects\services;

use src\app\data\Project\Project;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as DbFactory;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Project\ProjectRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\projects\events\ProjectAfterArchiveEvent;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use src\app\projects\interfaces\ProjectModelInterface;

class ArchiveProjectService
{
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;
    private $dbFactory;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        DbFactory $dbFactory
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->dbFactory = $dbFactory;
    }

    public function __invoke(ProjectModelInterface $model): void
    {
        $this->archive($model);
    }

    public function archive(ProjectModelInterface $model): void
    {
        $beforeEvent = new ProjectBeforeArchiveEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $record = $this->fetchRecord($model);

        $record->is_active = 0;

        $this->ormFactory->makeOrm()->persist($record);

        $afterEvent = new ProjectAfterArchiveEvent($model);

        $this->eventDispatcher->dispatch(
            $afterEvent->provider(),
            $afterEvent->name(),
            $afterEvent
        );
    }

    private function fetchRecord(ProjectModelInterface $model): ProjectRecord
    {
        $params = $this->dbFactory->makeQueryModel();
        $params->addWhere('guid', $model->guid());
        return $this->buildQuery->build(Project::class, $params)->fetchRecord();
    }
}
