<?php
declare(strict_types=1);

namespace src\app\projects\services;

use src\app\data\Project\Project;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Project\ProjectRecord;
use src\app\datasupport\BuildQueryInterface;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\projects\events\ProjectAfterDeleteEvent;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\projects\interfaces\ProjectModelInterface;

class DeleteProjectService
{
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;
    private $fetchDataParamsFactory;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        FetchDataParamsFactory $fetchDataParamsFactory
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->fetchDataParamsFactory = $fetchDataParamsFactory;
    }

    public function __invoke(ProjectModelInterface $model): void
    {
        $this->delete($model);
    }

    public function delete(ProjectModelInterface $model): void
    {
        $beforeEvent = new ProjectBeforeDeleteEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $afterEvent = new ProjectAfterDeleteEvent($model);

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
