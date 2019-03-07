<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use src\app\data\Pipeline\PipelineSelect;
use src\app\data\PipelineJob\PipelineJob;
use src\app\data\PipelineJob\PipelineJobRecord;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\PipelineItem\PipelineItemSelect;
use src\app\data\PipelineJobItem\PipelineJobItemSelect;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\transformers\PipelineJobRecordModelTransformer;

class FetchPipelineJobService
{
    private $buildQuery;
    private $pipelineJobRecordModelTransformer;

    public function __construct(
        BuildQueryInterface $buildQuery,
        PipelineJobRecordModelTransformer $pipelineJobRecordModelTransformer
    ) {
        $this->buildQuery = $buildQuery;
        $this->pipelineJobRecordModelTransformer = $pipelineJobRecordModelTransformer;
    }

    /**
     * @return PipelineJobModelInterface[]
     */
    public function __invoke(QueryModelInterface $params): array
    {
        return $this->fetch($params);
    }

    /**
     * @return PipelineJobModelInterface[]
     */
    public function fetch(QueryModelInterface $params): array
    {
        return $this->pipelineJobRecordModelTransformer->transformRecordSet(
            $this->fetchResults($params)
        );
    }

    /**
     * @param $params
     * @return PipelineJobRecord[]
     */
    private function fetchResults($params): array
    {
        $query = $this->buildQuery->build(PipelineJob::class, $params);

        $query->with([
            'pipeline' => static function (PipelineSelect $select) {
                $select->with([
                    'pipeline_items' => static function (PipelineItemSelect $select) {
                        $select->with(['servers']);
                        $select->orderBy('`order` ASC');
                    },
                ]);
            },
            'pipeline_job_items' => static function (PipelineJobItemSelect $select) {
                $select->orderBy('`order` ASC');

                $select->with([
                    'pipeline_item' => static function (PipelineItemSelect $select) {
                        $select->with(['servers']);
                        $select->orderBy('`order` ASC');
                    },
                ]);
            },
        ]);

        return $query->fetchRecords();
    }
}
