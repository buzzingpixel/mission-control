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
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('user_guid', 'binary', [
                'limit' => 16,
                'comment' => 'Associated user UUID stored as binary',
            ])
            ->addColumn('added_at', 'datetime', [
                'comment' => 'The datetime representation of when the session was added',
            ])
            ->addColumn('added_at_time_zone', 'string', [
                'comment' => 'The timezone added_at was set with',
            ])
            ->addColumn('last_touched_at', 'datetime', [
                'comment' => 'The datetime representation of when the session was last active',
            ])
            ->addColumn('last_touched_at_time_zone', 'string', [
                'comment' => 'The timezone last_touched_at was set with',
            ])
            ->create();
    }
}
