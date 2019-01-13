<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateActionQueueBatchTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('action_queue_batch')
            ->addColumn('guid', 'string')
            ->addColumn('name', 'string')
            ->addColumn('title', 'string')
            ->addColumn('has_started', 'boolean', ['default' => '0'])
            ->addColumn('is_finished', 'boolean', ['default' => '0'])
            ->addColumn('finished_due_to_error', 'boolean', ['default' => '0'])
            ->addColumn('percent_complete', 'float', ['default' => '0'])
            ->addColumn('added_at', 'datetime')
            ->addColumn('added_at_time_zone', 'string')
            ->addColumn('finished_at', 'datetime', ['null' => true])
            ->addColumn('finished_at_time_zone', 'string', ['null' => true])
            ->addColumn('context', 'text', ['null' => true])
            ->create();
    }
}
