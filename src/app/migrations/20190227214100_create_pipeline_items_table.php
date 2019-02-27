<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreatePipelineItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('pipeline_items', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('pipeline_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'Associated pipeline UUID stored as binary',
            ])
            ->addColumn('order', 'integer', [
                'comment' => 'The order of the items in the pipeline',
            ])
            ->addColumn('script', 'text', [
                'comment' => 'The script this item executes',
            ])
            ->create();
    }
}
