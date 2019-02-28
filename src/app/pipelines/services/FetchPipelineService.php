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

class FetchPipelineService
{
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
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

        foreach ($this->fetchResults($params) as $record) {
            $model = new PipelineModel();

            $model->setGuidAsBytes($record->guid);

            if ($record->project_guid) {
                $model->setProjectGuidAsBytes($record->project_guid);
            }

            $model->isActive($record->is_active === 1 || $record->is_active === '1');

            $model->title($record->title);

            $model->slug($record->slug);

            $model->description($record->description);

            $model->secretId($record->secret_id);

            foreach ($record->pipeline_items as $itemRecord) {
                /** @var PipelineItemRecord $itemRecord */

                $itemModel = new PipelineItemModel();

                $itemModel->setGuidAsBytes($itemRecord->guid);

                $itemModel->setPipelineGuidAsBytes($itemRecord->pipeline_guid);

                $itemModel->script($itemRecord->script);

                $model->addPipelineItem($itemModel);
            }

            $models[] = $model;
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
            },
        ]);

        return $query->fetchRecords();
    }
}
