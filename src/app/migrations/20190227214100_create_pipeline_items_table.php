<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreatePipelineItemsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('pipeline_items', [
            'id' => false,
            'primary_key' => ['guid'],
        ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('pipeline_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'Associated pipeline UUID stored as binary',
            ])
            ->addColumn('order', 'integer', ['comment' => 'The order of the items in the pipeline'])
            ->addColumn('description', 'string', [
                'null' => true,
                'comment' => 'Optional description',
            ])
            ->addColumn('script', 'text', ['comment' => 'The script this item executes'])
            ->create();
    }
}
