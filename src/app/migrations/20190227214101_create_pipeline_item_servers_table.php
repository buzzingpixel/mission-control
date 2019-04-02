<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreatePipelineItemServersTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('pipeline_item_servers', [
            'id' => false,
            'primary_key' => [
                'pipeline_item_guid',
                'server_guid',
            ],
        ])
            ->addColumn('pipeline_item_guid', 'binary', [
                'limit' => 16,
                'comment' => 'Associated pipeline item UUID stored as binary',
            ])
            ->addColumn('server_guid', 'binary', [
                'limit' => 16,
                'comment' => 'Associated sever UUID stored as binary',
            ])
            ->create();
    }
}
