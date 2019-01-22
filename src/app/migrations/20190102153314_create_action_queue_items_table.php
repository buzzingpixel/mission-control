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
            ->addColumn('guid', 'string')
            ->addColumn('order_to_run', 'integer')
            ->addColumn('action_queue_batch_guid', 'string')
            ->addColumn('is_finished', 'boolean', ['default' => '0'])
            ->addColumn('finished_at', 'datetime', ['null' => true])
            ->addColumn('finished_at_time_zone', 'string', ['null' => true])
            ->addColumn('class', 'text')
            ->addColumn('method', 'text')
            ->addColumn('context', 'text', ['null' => true])
            ->create();
    }
}
