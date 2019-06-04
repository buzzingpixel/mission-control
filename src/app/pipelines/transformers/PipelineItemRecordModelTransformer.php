<?php

declare(strict_types=1);

namespace src\app\pipelines\transformers;

use Atlas\Mapper\Record;
use src\app\data\PipelineItem\PipelineItemRecord;
use src\app\pipelines\interfaces\PipelineItemModelInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\models\PipelineItemModel;
use src\app\servers\transformers\ServerRecordModelTransformer;
use Traversable;
use function array_map;
use function is_array;
use function iterator_to_array;

class PipelineItemRecordModelTransformer
{
    /** @var ServerRecordModelTransformer */
    private $serverRecordModelTransformer;

    public function __construct(
        ServerRecordModelTransformer $serverRecordModelTransformer
    ) {
        $this->serverRecordModelTransformer = $serverRecordModelTransformer;
    }

    /**
     * @param Traversable|iterable|array|Record $recordSet
     *
     * @return array
     */
    public function transformRecordSet(
        $recordSet,
        PipelineModelInterface $pipelineModel
    ) : array {
        if ($recordSet === null) {
            return [];
        }

        $recordArray = is_array($recordSet) ?
            $recordSet :
            iterator_to_array($recordSet);

        return array_map(
            function (PipelineItemRecord $itemRecord) use ($pipelineModel) {
                // TODO: Dang. Deleted pipeline items causes unexpected issues
                if ($itemRecord instanceof PipelineItemRecord) {
                    return $this->transformRecord($itemRecord, $pipelineModel);
                }

                return new PipelineItemModel();
            },
            $recordArray
        );
    }

    public function transformRecord(
        ?PipelineItemRecord $itemRecord,
        PipelineModelInterface $pipelineModel
    ) : PipelineItemModelInterface {
        $itemModel = new PipelineItemModel();

        if (! $itemRecord) {
            return $itemModel;
        }

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
