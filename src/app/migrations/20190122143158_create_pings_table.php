<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreatePingsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('pings', [
            'id' => false,
            'primary_key' => ['guid'],
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
            ->addColumn('ping_id', 'string', [
                'limit' => 36,
                'comment' => 'The ping ID which will be used in URLs',
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => 1,
                'comment' => 'Determines whether the project is active or archived',
            ])
            ->addColumn('title', 'string', ['comment' => 'The project title'])
            ->addColumn('slug', 'string', ['comment' => 'The project slug (code should ensure uniqueness)'])
            ->addColumn('pending_error', 'boolean', [
                'default' => 0,
                'comment' => 'Set if a checkin is overdue',
            ])
            ->addColumn('has_error', 'boolean', [
                'default' => 0,
                'comment' => 'Set if a checkin is over and past the warn_after setting',
            ])
            ->addColumn('expect_every', 'biginteger', [
                'signed' => false,
                'comment' => 'Time in minutes to expect the checkin',
            ])
            ->addColumn('warn_after', 'biginteger', [
                'signed' => false,
                'comment' => 'Time in minutes to warn after overdue',
            ])
            ->addColumn('last_ping_at', 'datetime', ['comment' => 'The datetime representation of when the checkin last happened'])
            ->addColumn('last_ping_at_time_zone', 'string', ['comment' => 'The timezone last_ping_at was set with'])
            ->addColumn('last_notification_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when the down notification was last sent',
            ])
            ->addColumn('last_notification_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone last_notification_at was set with',
            ])
            ->addColumn('added_at', 'datetime', ['comment' => 'The datetime representation of when the ping was added'])
            ->addColumn('added_at_time_zone', 'string', ['comment' => 'The timezone added_at was set with'])
            ->create();
    }
}
