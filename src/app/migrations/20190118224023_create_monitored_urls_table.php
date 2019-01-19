<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateMonitoredUrlsTable extends AbstractMigration
{
    public function change()
    {
        $this->table('monitored_urls')
            ->addColumn('guid', 'string')
            ->addColumn('project_guid', 'string')
            ->addColumn('is_active', 'boolean', ['default' => '1'])
            ->addColumn('title', 'string')
            ->addColumn('slug', 'string')
            ->addColumn('url', 'string')
            ->addColumn('pending_error', 'boolean', ['default' => '0'])
            ->addColumn('has_error', 'boolean', ['default' => '0'])
            ->addColumn('checked_at', 'datetime')
            ->addColumn('checked_at_time_zone', 'string')
            ->addColumn('added_at', 'datetime')
            ->addColumn('added_at_time_zone', 'string')
            ->create();
    }
}
