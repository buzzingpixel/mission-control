<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreatePipelineJobItemsTable extends AbstractMigration
{
    public function change()
    {
        $this->table('pipeline_job_items', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
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
            ->addColumn('pipeline_job_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'Associated pipeline job UUID stored as binary',
            ])
            ->addColumn('pipeline_item_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'Associated pipeline item UUID stored as binary',
            ])
            ->addColumn('order', 'integer', [
                'comment' => 'The order of the items in the pipeline',
            ])
            ->addColumn('has_failed', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this job item indicated failure',
            ])
            ->addColumn('log_content', 'text', [
                'comment' => 'Log content',
            ])
            ->addColumn('finished_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when this job item was finished',
            ])
            ->addColumn('finished_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone finished_at was set with',
            ])
            ->create();
    }
}
