<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use src\app\data\Pipeline\Pipeline;
use src\app\data\Pipeline\PipelineRecord;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\PipelineItem\PipelineItemSelect;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\transformers\PipelineRecordModelTransformer;

class FetchPipelineService
{
    private $buildQuery;
    private $pipelineRecordModelTransformer;

    public function __construct(
        BuildQueryInterface $buildQuery,
        PipelineRecordModelTransformer $pipelineRecordModelTransformer
    ) {
        $this->buildQuery = $buildQuery;
        $this->pipelineRecordModelTransformer = $pipelineRecordModelTransformer;
    }

    /**
     * @return PipelineModelInterface[]
     */
    public function __invoke(QueryModelInterface $params): array
    {
        return $this->fetch($params);
    }

    /**
     * @return PipelineModelInterface[]
     */
    public function fetch(QueryModelInterface $params): array
    {
        return $this->pipelineRecordModelTransformer->transformRecordSet(
            $this->fetchResults($params)
        );
    }

    /**
     * @param $params
     * @return PipelineRecord[]
     */
    private function fetchResults($params): array
    {
        $query = $this->buildQuery->build(Pipeline::class, $params);

        $query->with([
            'pipeline_items' => function (PipelineItemSelect $select) {
                $select->orderBy('`order` ASC');

                $select->with([
                    'servers'
                ]);
            },
        ]);

        return $query->fetchRecords();
    }
}
