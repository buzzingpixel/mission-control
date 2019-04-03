<?php

declare(strict_types=1);

namespace src\app\projects\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\Project\Project;
use src\app\data\Project\ProjectRecord;
use src\app\projects\events\ProjectAfterDeleteEvent;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\projects\interfaces\ProjectModelInterface;

class DeleteProjectService
{
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ProjectModelInterface $model) : void
    {
        $this->delete($model);
    }

    public function delete(ProjectModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new ProjectBeforeDeleteEvent($model));

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $this->eventDispatcher->dispatch(new ProjectAfterDeleteEvent($model));
    }

    private function fetchRecord(ProjectModelInterface $model) : ProjectRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(Project::class, $params)->fetchRecord();
    }
}
