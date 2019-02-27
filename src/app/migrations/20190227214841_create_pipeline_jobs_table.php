<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreatePipelineJobsTable extends AbstractMigration
{
    public function change()
    {
        $this->table('pipeline_jobs', [
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
            ->addColumn('has_started', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this job has started or not',
            ])
            ->addColumn('is_finished', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this job is finished or not',
            ])
            ->addColumn('has_failed', 'boolean', [
                'default' => 0,
                'comment' => 'Whether this job indicated failure',
            ])
            ->addColumn('percent_complete', 'float', [
                'default' => 0,
                'comment' => 'The percentage of the job completed',
            ])
            ->addColumn('job_added_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when job was added',
            ])
            ->addColumn('job_added_at_time_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone job_added_at was set with',
            ])
            ->addColumn('job_finished_at', 'datetime', [
                'null' => true,
                'comment' => 'The datetime representation of when job was finished',
            ])
            ->addColumn('job_finished_at_zone', 'string', [
                'null' => true,
                'comment' => 'The timezone job_finished_at was set with',
            ])
            ->create();
    }
}
