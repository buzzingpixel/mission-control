<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateProjectsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('projects', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'string')
            ->addColumn('is_active', 'boolean', ['default' => '1'])
            ->addColumn('title', 'string')
            ->addColumn('slug', 'string')
            ->addColumn('description', 'string')
            ->addColumn('added_at', 'datetime')
            ->addColumn('added_at_time_zone', 'string')
            ->create();
    }
}
