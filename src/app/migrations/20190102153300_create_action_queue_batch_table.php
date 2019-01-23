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
        $this->table('action_queue_batch', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('name', 'string', [
                'comment' => 'The name of the batch for programmatic use',
            ])
            ->addColumn('title', 'string', [
                'comment' => 'The title of the batch for display purposes',
            ])
            ->addColumn('has_started', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this batch has started or not',
            ])
            ->addColumn('is_finished', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this batch has finished or not',
            ])
            ->addColumn('finished_due_to_error', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this batch was marked finished do to an error',
            ])
            ->addColumn('percent_complete', 'float', [
                'default' => 0,
                'comment' => 'The percentage of the batch completed',
            ])
            ->addColumn('added_at', 'datetime', [
                'comment' => 'The datetime representation of when the batch was added',
            ])
            ->addColumn('added_at_time_zone', 'string', [
                'comment' => 'The timezone added_at was set with',
            ])
            ->addColumn('finished_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when the batch was finished',
            ])
            ->addColumn('finished_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone finished_at was set with',
            ])
            ->addColumn('context', 'text', [
                'null' => true,
                'comment' => 'Not using JSON column type because of broad compatibility issues',
            ])
            ->create();
    }
}
