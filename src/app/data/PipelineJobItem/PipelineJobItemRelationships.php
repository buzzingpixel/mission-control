<?php
declare(strict_types=1);

namespace src\app\data\PipelineJobItem;

use src\app\data\Pipeline\Pipeline;
use Atlas\Mapper\MapperRelationships;
use src\app\data\PipelineJob\PipelineJob;
use src\app\data\PipelineItem\PipelineItem;

class PipelineJobItemRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('pipeline', Pipeline::class, [
            'pipeline_guid' => 'guid',
        ]);

        $this->manyToOne('pipeline_job', PipelineJob::class, [
            'pipeline_job_guid' => 'guid',
        ]);

        $this->manyToOne('pipeline_item', PipelineItem::class, [
            'pipeline_item_guid' => 'guid',
        ]);
    }
}
