<?php

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddPipelineTypeColumn extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('pipeline_items');

        $table->addColumn('type', 'string', [
            'after' => 'order',
            'null' => true,
            'default' => 'code',
        ]);

        $table->update();
    }
}
