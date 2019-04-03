<?php

declare(strict_types=1);

namespace src\app\pipelines\transformers;

use Atlas\Mapper\Record;
use DateTime;
use DateTimeZone;
use src\app\data\PipelineJob\PipelineJobRecord;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\models\PipelineJobModel;
use Traversable;
use function array_map;
use function is_array;
use function iterator_to_array;

class PipelineJobRecordModelTransformer
{
    /** @var PipelineRecordModelTransformer */
    private $pipelineRecordModelTransformer;
    /** @var PipelineJobItemRecordModelTransformer */
    private $pipelineJobItemRecordModelTransformer;

    public function __construct(
        PipelineRecordModelTransformer $pipelineRecordModelTransformer,
        PipelineJobItemRecordModelTransformer $pipelineJobItemRecordModelTransformer
    ) {
        $this->pipelineRecordModelTransformer        = $pipelineRecordModelTransformer;
        $this->pipelineJobItemRecordModelTransformer = $pipelineJobItemRecordModelTransformer;
    }

    /**
     * @param Traversable|iterable|array|Record $recordSet
     *
     * @return array
     */
    public function transformRecordSet($recordSet) : array
    {
        if ($recordSet === null) {
            return [];
        }

        $recordArray = is_array($recordSet) ?
            $recordSet :
            iterator_to_array($recordSet);

        return array_map(
            [
                $this,
                'transformRecord',
            ],
            $recordArray
        );
    }

    public function transformRecord(
        PipelineJobRecord $jobRecord
    ) : PipelineJobModelInterface {
        $jobModel = new PipelineJobModel();

        $jobModel->setGuidAsBytes($jobRecord->guid);

        $jobModel->pipeline(
            $this->pipelineRecordModelTransformer->transformRecord(
                $jobRecord->pipeline
            )
        );

        $jobModel->hasStarted(
            $jobRecord->has_started === 1 || $jobRecord->has_started === '1'
        );

        $jobModel->isFinished(
            $jobRecord->is_finished === 1 || $jobRecord->is_finished === '1'
        );

        $jobModel->hasFailed(
            $jobRecord->has_failed === 1 || $jobRecord->has_failed === '1'
        );

        $jobModel->percentComplete((float) $jobRecord->percent_complete);

        /** @noinspection PhpUnhandledExceptionInspection */
        $jobModel->jobAddedAt(new DateTime(
            $jobRecord->job_added_at,
            new DateTimeZone($jobRecord->job_added_at_time_zone)
        ));

        $jobFinishedAt = $jobRecord->job_finished_at;

        if ($jobFinishedAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $jobModel->jobFinishedAt(new DateTime(
                $jobFinishedAt,
                new DateTimeZone($jobRecord->job_added_at_time_zone)
            ));
        }

        $jobModel->pipelineJobItems(
            $this->pipelineJobItemRecordModelTransformer->transformRecordSet(
                $jobRecord->pipeline_job_items,
                $jobModel
            )
        );

        return $jobModel;
    }
}
