<?php
declare(strict_types=1);

namespace src\app\pipelines\transformers;

use Traversable;
use Atlas\Mapper\Record;
use src\app\pipelines\models\PipelineItemModel;
use src\app\data\PipelineItem\PipelineItemRecord;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\interfaces\PipelineItemModelInterface;
use src\app\servers\transformers\ServerRecordModelTransformer;

class PipelineItemRecordModelTransformer
{
    private $serverRecordModelTransformer;

    public function __construct(
        ServerRecordModelTransformer $serverRecordModelTransformer
    ) {
        $this->serverRecordModelTransformer = $serverRecordModelTransformer;
    }

    /**
     * @param Traversable|iterable|array|Record $recordSet
     * @return array
     */
    public function transformRecordSet(
        $recordSet,
        PipelineModelInterface $pipelineModel
    ): array {
        if ($recordSet === null) {
            return [];
        }

        $recordArray = is_array($recordSet) ?
            $recordSet :
            iterator_to_array($recordSet);

        return array_map(
            function (PipelineItemRecord $itemRecord) use ($pipelineModel) {
                return $this->transformRecord($itemRecord, $pipelineModel);
            },
            $recordArray
        );
    }

    public function transformRecord(
        PipelineItemRecord $itemRecord,
        PipelineModelInterface $pipelineModel
    ): PipelineItemModelInterface {
        $itemModel = new PipelineItemModel();

        $itemModel->setGuidAsBytes($itemRecord->guid);

        $itemModel->pipeline($pipelineModel);

        $itemModel->description($itemRecord->description);

        $itemModel->script($itemRecord->script);

        $itemModel->servers(
            $this->serverRecordModelTransformer->transformRecordSet(
                $itemRecord->servers
            )
        );

        return $itemModel;
    }
}
