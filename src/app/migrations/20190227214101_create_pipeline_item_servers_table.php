<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreatePipelineItemServersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('pipeline_item_servers', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
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
