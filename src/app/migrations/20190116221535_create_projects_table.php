<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateProjectsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('projects', [
            'id' => false,
            'primary_key' => ['guid'],
        ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => 1,
                'comment' => 'Determines whether the project is active or archived',
            ])
            ->addColumn('title', 'string', ['comment' => 'The project title'])
            ->addColumn('slug', 'string', ['comment' => 'The project slug (code should ensure uniqueness)'])
            ->addColumn('description', 'string', ['comment' => 'Optional project description'])
            ->addColumn('added_at', 'datetime', ['comment' => 'The datetime representation of when the projected was added'])
            ->addColumn('added_at_time_zone', 'string', ['comment' => 'The timezone added_at was set with'])
            ->create();
    }
}
