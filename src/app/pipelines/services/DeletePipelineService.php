<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use src\app\data\Pipeline\Pipeline;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Pipeline\PipelineRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\pipelines\events\PipelineAfterDeleteEvent;
use src\app\pipelines\events\PipelineBeforeDeleteEvent;
use src\app\pipelines\interfaces\PipelineModelInterface;

class DeletePipelineService
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
        $this->delete($model);
    }

    public function delete(PipelineModelInterface $model): void
    {
        $this->eventDispatcher->dispatch(new PipelineBeforeDeleteEvent($model));

        $orm = $this->ormFactory->makeOrm();

        $record = $this->fetchRecord($model);

        $orm->delete($record);

        foreach ($record->pipeline_items as $item) {
            $orm->delete($item);
        }

        $this->eventDispatcher->dispatch(new PipelineAfterDeleteEvent($model));
    }

    private function fetchRecord(PipelineModelInterface $model): PipelineRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        $query = $this->buildQuery->build(Pipeline::class, $params);

        $query->with([
            'pipeline_items',
        ]);

        return $query->fetchRecord();
    }
}
