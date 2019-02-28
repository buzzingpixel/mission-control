<?php
declare(strict_types=1);

namespace src\app\data\Pipeline;

use Atlas\Mapper\MapperRelationships;
use src\app\data\PipelineItem\PipelineItem;

class PipelineRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->oneToMany('pipeline_items', PipelineItem::class, [
            'guid' => 'pipeline_guid',
        ]);
    }
}
