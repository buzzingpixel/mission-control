<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use Atlas\Mapper\MapperSelect;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\PipelineJobItem\PipelineJobItem;
use src\app\data\PipelineJobItem\PipelineJobItemRecord;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;
use src\app\pipelines\transformers\PipelineJobRecordModelTransformer;
use src\app\pipelines\transformers\PipelineJobItemRecordModelTransformer;

class FetchOnePipelineJobItemService
{
    private $buildQuery;
    private $pipelineJobRecordModelTransformer;
    private $pipelineJobItemRecordModelTransformer;

    public function __construct(
        BuildQueryInterface $buildQuery,
        PipelineJobRecordModelTransformer $pipelineJobRecordModelTransformer,
        PipelineJobItemRecordModelTransformer $pipelineJobItemRecordModelTransformer
    ) {
        $this->buildQuery = $buildQuery;
        $this->pipelineJobRecordModelTransformer = $pipelineJobRecordModelTransformer;
        $this->pipelineJobItemRecordModelTransformer = $pipelineJobItemRecordModelTransformer;
    }

    public function __invoke(QueryModelInterface $params): PipelineJobItemModelInterface
    {
        return $this->fetch($params);
    }

    /**
     * @return PipelineJobItemModelInterface
     */
    public function fetch(QueryModelInterface $params): PipelineJobItemModelInterface
    {
        $params->limit(1);

        $record = $this->fetchResult($params);

        return $this->pipelineJobItemRecordModelTransformer->transformRecord(
            $record,
            $this->pipelineJobRecordModelTransformer->transformRecord($record->pipeline_job)
        );
    }

    private function fetchResult(QueryModelInterface $params): PipelineJobItemRecord
    {
        $query = $this->buildQuery->build(PipelineJobItem::class, $params);

        $query->with([
            'pipeline' => static function (MapperSelect $select) {
                $select->with(['pipeline_items']);
            },
            'pipeline_job' => static function (MapperSelect $select) {
                $select->with([
                    'pipeline' => static function (MapperSelect $select) {
                        $select->with(['pipeline_items']);
                    },
                ]);
            },
            'pipeline_item' => static function (MapperSelect $select) {
                $select->with([
                    'pipeline_item_servers',
                    'servers',
                ]);
            },
        ]);

        return $query->fetchRecord();
    }
}
