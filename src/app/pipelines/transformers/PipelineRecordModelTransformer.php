<?php
declare(strict_types=1);

namespace src\app\pipelines\transformers;

use Traversable;
use Atlas\Mapper\Record;
use src\app\data\Pipeline\PipelineRecord;
use src\app\pipelines\models\PipelineModel;
use src\app\pipelines\interfaces\PipelineModelInterface;

class PipelineRecordModelTransformer
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
    public function transformRecordSet($recordSet): array
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
                'transformRecord'
            ],
            $recordArray
        );
    }

    public function transformRecord(
        PipelineRecord $pipelineRecord
    ): PipelineModelInterface {
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

        $pipeline->pipelineItems(
            $this->pipelineItemRecordModelTransformer->transformRecordSet(
                $pipelineRecord->pipeline_items,
                $pipeline
            )
        );

        return $pipeline;
    }
}
