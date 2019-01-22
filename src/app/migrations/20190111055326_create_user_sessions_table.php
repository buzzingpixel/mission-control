<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateUserSessionsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('user_sessions', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'string')
            ->addColumn('user_guid', 'text')
            ->addColumn('added_at', 'datetime')
            ->addColumn('added_at_time_zone', 'string')
            ->addColumn('last_touched_at', 'datetime')
            ->addColumn('last_touched_at_time_zone', 'string')
            ->create();
    }
}
