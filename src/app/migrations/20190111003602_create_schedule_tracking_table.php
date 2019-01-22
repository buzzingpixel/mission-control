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
                    'guid'
                ]
            ])
            ->addColumn('guid', 'string')
            ->addColumn('is_running', 'boolean', ['default' => '0'])
            ->addColumn('last_run_start_at', 'datetime', ['null' => true])
            ->addColumn('last_run_start_at_time_zone', 'string', ['null' => true])
            ->addColumn('last_run_end_at', 'datetime', ['null' => true])
            ->addColumn('last_run_end_at_time_zone', 'string', ['null' => true])
            ->create();
    }
}
