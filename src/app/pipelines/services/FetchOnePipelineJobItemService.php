<?php

declare(strict_types=1);

namespace src\app\pipelines\services;

use Atlas\Mapper\MapperSelect;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\data\PipelineJobItem\PipelineJobItem;
use src\app\data\PipelineJobItem\PipelineJobItemRecord;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;
use src\app\pipelines\transformers\PipelineJobItemRecordModelTransformer;
use src\app\pipelines\transformers\PipelineJobRecordModelTransformer;

class FetchOnePipelineJobItemService
{
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var PipelineJobRecordModelTransformer */
    private $pipelineJobRecordModelTransformer;
    /** @var PipelineJobItemRecordModelTransformer */
    private $pipelineJobItemRecordModelTransformer;

    public function __construct(
        BuildQueryInterface $buildQuery,
        PipelineJobRecordModelTransformer $pipelineJobRecordModelTransformer,
        PipelineJobItemRecordModelTransformer $pipelineJobItemRecordModelTransformer
    ) {
        $this->buildQuery                            = $buildQuery;
        $this->pipelineJobRecordModelTransformer     = $pipelineJobRecordModelTransformer;
        $this->pipelineJobItemRecordModelTransformer = $pipelineJobItemRecordModelTransformer;
    }

    public function __invoke(QueryModelInterface $params) : PipelineJobItemModelInterface
    {
        return $this->fetch($params);
    }

    public function fetch(QueryModelInterface $params) : PipelineJobItemModelInterface
    {
        $params->limit(1);

        $record = $this->fetchResult($params);

        return $this->pipelineJobItemRecordModelTransformer->transformRecord(
            $record,
            $this->pipelineJobRecordModelTransformer->transformRecord($record->pipeline_job)
        );
    }

    private function fetchResult(QueryModelInterface $params) : PipelineJobItemRecord
    {
        $query = $this->buildQuery->build(PipelineJobItem::class, $params);

        /** @noinspection PhpUnhandledExceptionInspection */
        $query->with([
            'pipeline' => static function (MapperSelect $select) : void {
                $select->with(['pipeline_items']);
            },
            'pipeline_job' => static function (MapperSelect $select) : void {
                $select->with([
                    'pipeline' => static function (MapperSelect $select) : void {
                        $select->with(['pipeline_items']);
                    },
                ]);
            },
            'pipeline_item' => static function (MapperSelect $select) : void {
                $select->with([
                    'pipeline_item_servers',
                    'servers',
                ]);
            },
        ]);

        return $query->fetchRecord();
    }
}
