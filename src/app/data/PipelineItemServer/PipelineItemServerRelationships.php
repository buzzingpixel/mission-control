<?php
declare(strict_types=1);

namespace src\app\data\PipelineItemServer;

use src\app\data\Server\Server;
use Atlas\Mapper\MapperRelationships;
use src\app\data\PipelineItem\PipelineItem;

class PipelineItemServerRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('pipeline_items', PipelineItem::class, [
            'pipeline_item_guid' => 'guid',
        ]);

        $this->manyToOne('servers', Server::class, [
            'server_guid' => 'guid',
        ]);
    }
}
