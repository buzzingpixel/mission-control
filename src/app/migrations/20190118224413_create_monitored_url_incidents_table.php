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
    public function change()
    {
        $this->table('monitored_url_incidents')
            ->addColumn('guid', 'string')
            ->addColumn('monitored_url_guid', 'string')
            ->addColumn('event_type', 'string')
            ->addColumn('status_code', 'string')
            ->addColumn('message', 'text')
            ->addColumn('event_at', 'datetime')
            ->addColumn('event_at_time_zone', 'string')
            ->create();
    }
}
