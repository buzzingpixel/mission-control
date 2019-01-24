<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateRemindersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('reminders', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('project_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'Associated project UUID stored as binary',
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => 1,
                'comment' => 'Determines whether the reminder is active or archived',
            ])
            ->addColumn('title', 'string', [
                'comment' => 'The reminder title',
            ])
            ->addColumn('slug', 'string', [
                'comment' => 'The reminder slug (code should ensure uniqueness)',
            ])
            ->addColumn('message', 'string', [
                'comment' => 'The reminder message',
            ])
            ->addColumn('added_at', 'datetime', [
                'comment' => 'The datetime representation of when the reminder was added',
            ])
            ->addColumn('added_at_time_zone', 'string', [
                'comment' => 'The timezone added_at was set with',
            ])
            ->create();
    }
}
