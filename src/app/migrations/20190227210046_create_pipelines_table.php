<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreatePipelinesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('pipelines', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('project_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'Associated project UUID stored as binary',
            ])
            ->addColumn('title', 'string', [
                'comment' => 'The title',
            ])
            ->addColumn('slug', 'string', [
                'comment' => 'The slug (code should ensure uniqueness)',
            ])
            ->addColumn('description', 'string', [
                'comment' => 'Optional description',
            ])
            ->addColumn('secret_id', 'string', [
                'comment' => 'Secret id to be used in URI to trigger pipeline',
            ])
            ->create();
    }
}
