<?php
declare(strict_types=1);

namespace src\app\pipelines\transformers;

use DateTime;
use Traversable;
use DateTimeZone;
use Atlas\Mapper\Record;
use src\app\pipelines\models\PipelineJobItemModel;
use src\app\data\PipelineJobItem\PipelineJobItemRecord;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;

class PipelineJobItemRecordModelTransformer
{
    private $pipelineItemRecordModelTransformer;

    public function __construct(
        PipelineItemRecordModelTransformer $pipelineItemRecordModelTransformer
    ) {
        $this->pipelineItemRecordModelTransformer = $pipelineItemRecordModelTransformer;
    }

    /**
     * @param Traversable|iterable|array|Record $recordSet
     * @return array
     */
    public function transformRecordSet(
        $recordSet,
        PipelineJobModelInterface $jobModel
    ): array {
        if ($recordSet === null) {
            return [];
        }

        $recordArray = is_array($recordSet) ?
            $recordSet :
            iterator_to_array($recordSet);

        return array_map(
            function (PipelineJobItemRecord $itemRecord) use ($jobModel) {
                return $this->transformRecord($itemRecord, $jobModel);
            },
            $recordArray
        );
    }

    public function transformRecord(
        PipelineJobItemRecord $itemRecord,
        PipelineJobModelInterface $jobModel
    ): PipelineJobItemModelInterface {
        $itemModel = new PipelineJobItemModel();

        $itemModel->setGuidAsBytes($itemRecord->guid);

        $itemModel->pipeline($jobModel->pipeline());

        $itemModel->pipelineJob($jobModel);

        $itemModel->pipelineItem(
            $this->pipelineItemRecordModelTransformer->transformRecord(
                $itemRecord->pipeline_item,
                $itemModel->pipeline()
            )
        );

        $itemModel->hasFailed(
            $itemRecord->has_failed === 1 || $itemRecord->has_failed === '1'
        );

        $itemModel->logContent($itemRecord->log_content);

        if ($finishedAt = $itemRecord->finished_at) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $itemModel->finishedAt(new DateTime(
                $finishedAt,
                new DateTimeZone($itemRecord->finished_at_time_zone)
            ));
        }

        return $itemModel;
    }
}
