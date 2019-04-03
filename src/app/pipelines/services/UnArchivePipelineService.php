<?php

declare(strict_types=1);

namespace src\app\pipelines\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\Pipeline\Pipeline;
use src\app\data\Pipeline\PipelineRecord;
use src\app\pipelines\events\PipelineAfterUnArchiveEvent;
use src\app\pipelines\events\PipelineBeforeUnArchiveEvent;
use src\app\pipelines\interfaces\PipelineModelInterface;

class UnArchivePipelineService
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

    public function __invoke(PipelineModelInterface $model) : void
    {
        $this->unArchive($model);
    }

    public function unArchive(PipelineModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new PipelineBeforeUnArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new PipelineAfterUnArchiveEvent($model));
    }

    private function fetchRecord(PipelineModelInterface $model) : PipelineRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(Pipeline::class, $params)->fetchRecord();
    }
}
