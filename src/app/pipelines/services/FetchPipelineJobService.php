<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use DateTime;
use DateTimeZone;
use src\app\data\Pipeline\PipelineSelect;
use src\app\data\PipelineItem\PipelineItemSelect;
use src\app\data\PipelineJob\PipelineJob;
use src\app\pipelines\models\PipelineModel;
use src\app\pipelines\models\PipelineItemModel;
use src\app\pipelines\models\PipelineJobModel;
use src\app\data\PipelineJob\PipelineJobRecord;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\pipelines\models\PipelineJobItemModel;
use src\app\data\PipelineJobItem\PipelineJobItemRecord;
use src\app\data\PipelineJobItem\PipelineJobItemSelect;
use src\app\pipelines\interfaces\PipelineJobModelInterface;

class FetchPipelineJobService
{
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
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
        $models = [];

        foreach ($this->fetchResults($params) as $jobRecord) {
            $jobModel = new PipelineJobModel();

            $jobModel->setGuidAsBytes($jobRecord->guid);

            $pipeline = new PipelineModel();

            if ($pipelineRecord = $jobRecord->pipeline) {
                $pipeline->setGuidAsBytes($pipelineRecord->guid);

                $pipeline->projectGuid($pipelineRecord->project_guid);

                $pipeline->isActive($pipelineRecord->is_active === 1 || $pipelineRecord->is_active === '1');

                $pipeline->title($pipelineRecord->title);

                $pipeline->slug($pipelineRecord->slug);

                $pipeline->description($pipelineRecord->description);

                $pipeline->secretId($pipelineRecord->secret_id);

                foreach ($pipelineRecord->pipeline_items as $pipelineItemRecord) {
                    $pipelineItem = new PipelineItemModel();

                    $pipelineItem->setGuidAsBytes($pipelineItemRecord->guid);

                    $pipelineItem->pipeline($pipeline);

                    $pipelineItem->script($pipelineItemRecord->script);

                    $pipeline->addPipelineItem($pipelineItem);
                }
            }

            $jobModel->pipeline($pipeline);

            $jobModel->hasStarted($jobRecord->has_started === 1 || $jobRecord->has_started === '1');

            $jobModel->isFinished($jobRecord->is_finished === 1 || $jobRecord->is_finished === '1');

            $jobModel->hasFailed($jobRecord->has_failed === 1 || $jobRecord->has_failed === '1');

            $jobModel->percentComplete((float) $jobRecord->percent_complete);

            /** @noinspection PhpUnhandledExceptionInspection */
            $jobModel->jobAddedAt(new DateTime(
                $jobRecord->job_added_at,
                new DateTimeZone($jobRecord->job_added_at_time_zone)
            ));

            if ($jobFinishedAt = $jobRecord->job_finished_at) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $jobModel->jobFinishedAt(new DateTime(
                    $jobFinishedAt,
                    new DateTimeZone($jobRecord->job_added_at_time_zone)
                ));
            }

            foreach ($jobRecord->pipeline_job_items as $itemRecord) {
                /** @var PipelineJobItemRecord $itemRecord */

                $itemModel = new PipelineJobItemModel();

                $itemModel->setGuidAsBytes($itemRecord->guid);

                $itemModel->pipeline($pipeline);

                $itemModel->pipelineJob($jobModel);

                $pipelineItem = new PipelineItemModel();

                if ($pipelineItemRecord = $itemRecord->pipeline_item) {
                    $continue = true;

                    foreach ($pipeline->pipelineItems() as $testPipelineItem) {
                        if ($testPipelineItem->getGuidAsBytes() !== $pipelineItemRecord->guid) {
                            continue;
                        }

                        $pipelineItem = $testPipelineItem;

                        $continue = false;

                        break;
                    }

                    if ($continue) {
                        $pipelineItem->setGuidAsBytes($itemRecord->guid);

                        $pipelineItem->pipeline($pipeline);

                        $pipelineItem->script($pipelineItemRecord->script);
                    }
                }

                $itemModel->pipelineItem($pipelineItem);

                $itemModel->hasFailed($itemRecord->has_failed === 1 || $itemRecord->has_failed === '1');

                $itemModel->logContent($itemRecord->log_content);

                if ($finishedAt = $itemRecord->finished_at) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $itemModel->finishedAt(new DateTime(
                        $finishedAt,
                        new DateTimeZone($itemRecord->finished_at_time_zone)
                    ));
                }

                $jobModel->addPipelineJobItem($itemModel);
            }

            $models[] = $jobModel;
        }

        return $models;
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
                    'pipeline_items' => function (PipelineItemSelect $select) {
                        $select->orderBy('`order` ASC');
                    },
                ]);
            },
            'pipeline_job_items' => static function (PipelineJobItemSelect $select) {
                $select->orderBy('`order` ASC');

                $select->with([
                    'pipeline_item'
                ]);
            },
        ]);

        return $query->fetchRecords();
    }
}
