<?php
declare(strict_types=1);

namespace src\app\data\PipelineItem;

use src\app\data\Pipeline\Pipeline;
use Atlas\Mapper\MapperRelationships;

class PipelineItemRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('pipeline', Pipeline::class, [
            'pipeline_guid' => 'guid',
        ]);
    }
}
