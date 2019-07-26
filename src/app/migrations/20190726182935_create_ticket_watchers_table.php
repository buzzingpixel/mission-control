<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateTicketWatchersTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('ticket_watchers', [
            'id' => false,
            'primary_key' => ['guid'],
        ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('ticket_guid', 'binary', [
                'null' => true,
                'limit' => 16,
            ])
            ->addColumn('user_guid', 'binary', [
                'null' => true,
                'limit' => 16,
            ])
            ->create();
    }
}
