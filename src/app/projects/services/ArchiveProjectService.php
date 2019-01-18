<?php
declare(strict_types=1);

namespace src\app\projects\services;

use corbomite\db\PDO;
use src\app\data\Project\Project;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Project\ProjectRecord;
use src\app\datasupport\BuildQueryInterface;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\projects\events\ProjectAfterArchiveEvent;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use src\app\projects\interfaces\ProjectModelInterface;

class ArchiveProjectService
{
    private $pdo;
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;
    private $fetchDataParamsFactory;

    public function __construct(
        PDO $pdo,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        FetchDataParamsFactory $fetchDataParamsFactory
    ) {
        $this->pdo = $pdo;
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->fetchDataParamsFactory = $fetchDataParamsFactory;
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
        $params = $this->fetchDataParamsFactory->make();
        $params->addWhere('guid', $model->guid());
        return $this->buildQuery->build(Project::class, $params)->fetchRecord();
    }
}
