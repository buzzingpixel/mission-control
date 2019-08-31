<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddProjectKeyValueItemsField extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('projects');

        $table->addColumn('key_value_items', 'text', [
            'after' => 'description',
            'null' => true,
            'comment' => 'Not using JSON column type because of broad compatibility issues',
        ]);

        $table->update();
    }
}
