<?php
declare(strict_types=1);

namespace src\app\data\PipelineJobItem;

use Atlas\Mapper\MapperRelationships;
use src\app\data\PipelineJob\PipelineJob;

class PipelineJobItemRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('pipeline_job', PipelineJob::class, [
            'pipeline_job_guid' => 'guid',
        ]);
    }
}
