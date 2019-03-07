<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use src\app\data\Pipeline\Pipeline;
use src\app\data\Pipeline\PipelineRecord;
use src\app\pipelines\models\PipelineModel;
use src\app\pipelines\models\PipelineItemModel;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\PipelineItem\PipelineItemSelect;
use src\app\data\PipelineItem\PipelineItemRecord;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\servers\transformers\ServerRecordModelTransformer;

class FetchPipelineService
{
    private $buildQuery;
    private $serverRecordModelTransformer;

    public function __construct(
        BuildQueryInterface $buildQuery,
        ServerRecordModelTransformer $serverRecordModelTransformer
    ) {
        $this->buildQuery = $buildQuery;
        $this->serverRecordModelTransformer = $serverRecordModelTransformer;
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
        $models = [];

        foreach ($this->fetchResults($params) as $pipelineRecord) {
            $pipeline = new PipelineModel();

            $pipeline->setGuidAsBytes($pipelineRecord->guid);

            if ($pipelineRecord->project_guid) {
                $pipeline->setProjectGuidAsBytes($pipelineRecord->project_guid);
            }

            $pipeline->isActive($pipelineRecord->is_active === 1 || $pipelineRecord->is_active === '1');

            $pipeline->title($pipelineRecord->title);

            $pipeline->slug($pipelineRecord->slug);

            $pipeline->description($pipelineRecord->description);

            $pipeline->secretId($pipelineRecord->secret_id);

            foreach ($pipelineRecord->pipeline_items as $itemRecord) {
                /** @var PipelineItemRecord $itemRecord */

                $itemModel = new PipelineItemModel();

                $itemModel->setGuidAsBytes($itemRecord->guid);

                $itemModel->pipeline($pipeline);

                $itemModel->description($itemRecord->description);

                $itemModel->script($itemRecord->script);

                $itemModel->servers(
                    $this->serverRecordModelTransformer->transformRecordSet(
                        $itemRecord->servers
                    )
                );

                $pipeline->addPipelineItem($itemModel);
            }

            $models[] = $pipeline;
        }

        return $models;
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
