<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddRunAfterFailColumnToPipelineItems extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('pipeline_items');

        $table->addColumn('run_after_fail', 'boolean', [
            'after' => 'script',
            'default' => 0,
            'comment' => 'Whether this item should run even after batch failure',
        ])
        ->update();
    }
}
