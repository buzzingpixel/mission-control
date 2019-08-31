<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateTicketThreadItemsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('ticket_thread_items', [
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
            ->addColumn('content', 'text', ['null' => true])
            ->addColumn('added_at_utc', 'datetime')
            ->addColumn('has_been_modified', 'boolean', ['default' => 0])
            ->addColumn('modified_at_utc', 'datetime', ['null' => true])
            ->create();
    }
}
