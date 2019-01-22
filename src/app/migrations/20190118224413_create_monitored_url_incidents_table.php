<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateMonitoredUrlIncidentsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('monitored_url_incidents', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('monitored_url_guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('event_type', 'string', [
                'comment' => 'Up or down or pending at initial writing of app',
            ])
            ->addColumn('status_code', 'string', [
                'comment' => 'The status code returned by the URL',
            ])
            ->addColumn('message', 'text', [
                'comment' => 'The message returned from the URL',
            ])
            ->addColumn('event_at', 'datetime', [
                'comment' => 'The datetime representation of when the event occurred',
            ])
            ->addColumn('event_at_time_zone', 'string', [
                'comment' => 'The timezone event_at was set with',
            ])
            ->create();
    }
}
