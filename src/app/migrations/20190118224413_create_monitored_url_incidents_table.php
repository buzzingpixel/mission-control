<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateMonitoredUrlIncidentsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('monitored_url_incidents', [
            'id' => false,
            'primary_key' => ['guid'],
        ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('monitored_url_guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('event_type', 'string', ['comment' => 'Up or down or pending at initial writing of app'])
            ->addColumn('status_code', 'string', ['comment' => 'The status code returned by the URL'])
            ->addColumn('message', 'text', ['comment' => 'The message returned from the URL'])
            ->addColumn('event_at', 'datetime', ['comment' => 'The datetime representation of when the event occurred'])
            ->addColumn('event_at_time_zone', 'string', ['comment' => 'The timezone event_at was set with'])
            ->addColumn('last_notification_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when the last notification was sent',
            ])
            ->addColumn('last_notification_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone last_notification_at was set with',
            ])
            ->create();
    }
}
