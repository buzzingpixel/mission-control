<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateMonitoredUrlsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('monitored_urls', [
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
            ->addColumn('is_active', 'boolean', [
                'default' => 1,
                'comment' => 'Determines whether the project is active or archived',
            ])
            ->addColumn('title', 'string', ['comment' => 'The project title'])
            ->addColumn('slug', 'string', ['comment' => 'The project slug (code should ensure uniqueness)'])
            ->addColumn('url', 'string', ['comment' => 'Url to monitor'])
            ->addColumn('pending_error', 'boolean', [
                'default' => 0,
                'comment' => 'If an error was detected on check for the first time, status will be pending',
            ])
            ->addColumn('has_error', 'boolean', [
                'default' => 0,
                'comment' => 'If an error is detected a second time after pending, monitored URL has an error',
            ])
            ->addColumn('checked_at', 'datetime', ['comment' => 'The datetime representation of when the URL was last checked'])
            ->addColumn('checked_at_time_zone', 'string', ['comment' => 'The timezone checked_at was set with'])
            ->addColumn('added_at', 'datetime', ['comment' => 'The datetime representation of when the projected was added'])
            ->addColumn('added_at_time_zone', 'string', ['comment' => 'The timezone added_at was set with'])
            ->create();
    }
}
