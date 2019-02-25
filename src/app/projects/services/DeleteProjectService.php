<?php
declare(strict_types=1);

namespace src\app\projects\services;

use src\app\data\Project\Project;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Project\ProjectRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\projects\events\ProjectAfterDeleteEvent;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\projects\interfaces\ProjectModelInterface;

class DeleteProjectService
{
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ProjectModelInterface $model): void
    {
        $this->delete($model);
    }

    public function delete(ProjectModelInterface $model): void
    {
        $this->eventDispatcher->dispatch(new ProjectBeforeDeleteEvent($model));

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $this->eventDispatcher->dispatch(new ProjectAfterDeleteEvent($model));
    }

    private function fetchRecord(ProjectModelInterface $model): ProjectRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(Project::class, $params)->fetchRecord();
    }
}
