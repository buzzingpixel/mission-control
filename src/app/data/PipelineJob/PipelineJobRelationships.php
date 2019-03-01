<?php
declare(strict_types=1);

namespace src\app\data\PipelineJob;

use src\app\data\Pipeline\Pipeline;
use Atlas\Mapper\MapperRelationships;
use src\app\data\PipelineJobItem\PipelineJobItem;

class PipelineJobRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('pipeline', Pipeline::class, [
            'pipeline_guid' => 'guid',
        ]);

        $this->oneToMany('pipeline_job_items', PipelineJobItem::class, [
            'guid' => 'pipeline_job_guid',
        ]);
    }
}
