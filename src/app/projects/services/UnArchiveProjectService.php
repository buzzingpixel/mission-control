<?php

declare(strict_types=1);

namespace src\app\projects\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\Project\Project;
use src\app\data\Project\ProjectRecord;
use src\app\projects\events\ProjectAfterUnArchiveEvent;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;
use src\app\projects\interfaces\ProjectModelInterface;

class UnArchiveProjectService
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
        $this->unArchive($model);
    }

    public function unArchive(ProjectModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new ProjectBeforeUnArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new ProjectAfterUnArchiveEvent($model));
    }

    private function fetchRecord(ProjectModelInterface $model) : ProjectRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(Project::class, $params)->fetchRecord();
    }
}
