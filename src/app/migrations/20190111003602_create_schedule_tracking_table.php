<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateScheduleTrackingTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('schedule_tracking', [
                'id' => false,
                'primary_key' => [
                    'id'
                ]
            ])
            ->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => false,
                'comment' => 'Auto incrementing primary key as big int for future proofing',
            ])
            ->addColumn('guid', 'string', [
                'comment' => 'UUID generated in code',
            ])
            ->addColumn('is_running', 'boolean', [
                'default' => 0,
                'comment' => 'Whether the scheduled task is running',
            ])
            ->addColumn('last_run_start_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when the last run started',
            ])
            ->addColumn('last_run_start_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone last_run_start_at was set with',
            ])
            ->addColumn('last_run_end_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when the last run ended',
            ])
            ->addColumn('last_run_end_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone last_run_end_at was set with',
            ])
            ->create();
    }
}
