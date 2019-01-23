<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateActionQueueItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('action_queue_items', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('action_queue_batch_guid', 'binary', [
                'limit' => 16,
                'comment' => 'Associated batch UUID stored as binary',
            ])
            ->addColumn('order_to_run', 'integer', [
                'comment' => 'The order in which the items in this batch should run',
            ])
            ->addColumn('is_finished', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this batch has finished or not',
            ])
            ->addColumn('finished_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when the item was finished',
            ])
            ->addColumn('finished_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone finished_at was set with',
            ])
            ->addColumn('class', 'text', [
                'comment' => 'The class that this item will use to run',
            ])
            ->addColumn('method', 'text', [
                'comment' => 'The method on the class that this item will use to run',
            ])
            ->addColumn('context', 'text', [
                'null' => true,
                'comment' => 'Not using JSON column type because of broad compatibility issues',
            ])
            ->create();
    }
}
