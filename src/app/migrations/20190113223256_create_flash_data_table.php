<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateFlashDataTable extends AbstractMigration
{
    public function change()
    {
        $this->table('flash_data')
            ->addColumn('guid', 'string')
            ->addColumn('name', 'text', ['null' => true])
            ->addColumn('data', 'text', ['null' => true])
            ->addColumn('added_at', 'datetime', ['null' => true])
            ->addColumn('added_at_time_zone', 'string', ['null' => true])
            ->create();
    }
}
