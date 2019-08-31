<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddErrorMessageColToBatchTable extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('action_queue_batch');

        $table->addColumn('error_message', 'text', [
            'after' => 'finished_due_to_error',
            'null' => true,
            'comment' => 'If error message, it will be added to this column',
        ])
        ->update();
    }
}
