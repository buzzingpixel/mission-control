<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use src\app\data\Pipeline\Pipeline;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Pipeline\PipelineRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\pipelines\events\PipelineAfterArchiveEvent;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\events\PipelineBeforeArchiveEvent;

class ArchivePipelineService
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

    public function __invoke(PipelineModelInterface $model): void
    {
        $this->archive($model);
    }

    public function archive(PipelineModelInterface $model): void
    {
        $this->eventDispatcher->dispatch(new PipelineBeforeArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 0;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new PipelineAfterArchiveEvent($model));
    }

    private function fetchRecord(PipelineModelInterface $model): PipelineRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(Pipeline::class, $params)->fetchRecord();
    }
}
