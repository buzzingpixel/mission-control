<?php

declare(strict_types=1);

namespace src\app\pipelines\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\data\Pipeline\Pipeline;
use src\app\data\Pipeline\PipelineRecord;
use src\app\data\PipelineItem\PipelineItemSelect;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\transformers\PipelineRecordModelTransformer;

class FetchPipelineService
{
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var PipelineRecordModelTransformer */
    private $pipelineRecordModelTransformer;

    public function __construct(
        BuildQueryInterface $buildQuery,
        PipelineRecordModelTransformer $pipelineRecordModelTransformer
    ) {
        $this->buildQuery                     = $buildQuery;
        $this->pipelineRecordModelTransformer = $pipelineRecordModelTransformer;
    }

    /**
     * @return PipelineModelInterface[]
     */
    public function __invoke(QueryModelInterface $params) : array
    {
        return $this->fetch($params);
    }

    /**
     * @return PipelineModelInterface[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        return $this->pipelineRecordModelTransformer->transformRecordSet(
            $this->fetchResults($params)
        );
    }

    /**
     * @return PipelineRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        $query = $this->buildQuery->build(Pipeline::class, $params);

        /** @noinspection PhpUnhandledExceptionInspection */
        $query->with([
            'pipeline_items' => static function (PipelineItemSelect $select) : void {
                $select->orderBy('`order` ASC');

                $select->with(['servers']);
            },
        ]);

        return $query->fetchRecords();
    }
}
