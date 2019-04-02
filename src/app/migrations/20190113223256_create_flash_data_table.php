<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateFlashDataTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('flash_data', [
            'id' => false,
            'primary_key' => ['guid'],
        ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('name', 'text', [
                'null' => true,
                'comment' => 'Flash Data name',
            ])
            ->addColumn('data', 'text', [
                'null' => true,
                'comment' => 'Flash Data stored as JSON',
            ])
            ->addColumn('added_at', 'datetime', ['comment' => 'The datetime representation of when the flash data was added'])
            ->addColumn('added_at_time_zone', 'string', ['comment' => 'The timezone added_at was set with'])
            ->create();
    }
}
