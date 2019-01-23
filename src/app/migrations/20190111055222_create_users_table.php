<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('email_address', 'text', [
                'comment' => 'User\'s email address',
            ])
            ->addColumn('password_hash', 'string', [
                'comment' => 'User\'s hashed password',
            ])
            ->addColumn('user_data', 'text', [
                'comment' => 'User data for app development stored as JSON',
            ])
            ->addColumn('added_at', 'datetime', [
                'comment' => 'The datetime representation of when the user was added',
            ])
            ->addColumn('added_at_time_zone', 'string', [
                'comment' => 'The timezone added_at was set with',
            ])
            ->create();
    }
}
