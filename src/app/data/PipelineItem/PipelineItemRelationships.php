<?php
declare(strict_types=1);

namespace src\app\data\PipelineItem;

use src\app\data\Server\Server;
use src\app\data\Pipeline\Pipeline;
use Atlas\Mapper\MapperRelationships;
use src\app\data\PipelineItemServer\PipelineItemServer;

class PipelineItemRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('pipeline', Pipeline::class, [
            'pipeline_guid' => 'guid',
        ]);

        $this->oneToMany('pipeline_item_servers', PipelineItemServer::CLASS, [
            'guid' => 'pipeline_item_guid',
        ]);

        $this->manyToMany('servers', Server::CLASS, 'pipeline_item_servers', [
            'server_guid' => 'guid'
        ]);
    }
}
