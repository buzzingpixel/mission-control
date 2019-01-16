<?php


use Phinx\Migration\AbstractMigration;

class CreateProjectsTable extends AbstractMigration
{
    public function change()
    {
        $this->table('projects')
            ->addColumn('guid', 'string')
            ->addColumn('is_active', 'boolean', ['default' => '1'])
            ->addColumn('title', 'string')
            ->addColumn('slug', 'string')
            ->addColumn('description', 'string')
            ->addColumn('added_at', 'datetime')
            ->addColumn('added_at_time_zone', 'string')
            ->create();
    }
}
