<?php
declare(strict_types=1);

namespace src\app\data\PipelineJob;

use Atlas\Mapper\MapperRelationships;
use src\app\data\PipelineJobItem\PipelineJobItem;

class PipelineJobRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->oneToMany('pipeline_job_items', PipelineJobItem::class, [
            'guid' => 'pipeline_job_guid',
        ]);
    }
}
