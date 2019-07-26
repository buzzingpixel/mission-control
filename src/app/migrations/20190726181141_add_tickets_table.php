<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddTicketsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('tickets', [
            'id' => false,
            'primary_key' => ['guid'],
        ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('created_by_user_guid', 'binary', [
                'null' => true,
                'limit' => 16,
            ])
            ->addColumn('assigned_to_user_guid', 'binary', [
                'null' => true,
                'limit' => 16,
            ])
            ->addColumn('title', 'text', ['null' => true])
            ->addColumn('content', 'text', ['null' => true])
            ->addColumn('status', 'enum', [
                'default' => 'new',
                'values' => [
                    'new',
                    'in_progress',
                    'on_hold',
                    'resolved',
                ],
            ])
            ->addColumn('added_at_utc', 'datetime')
            ->addColumn('resolved_at_utc', 'datetime', ['null' => true])
            ->create();
    }
}
