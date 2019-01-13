<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    public function change()
    {
        $this->table('users')
            ->addColumn('guid', 'string')
            ->addColumn('email_address', 'text')
            ->addColumn('password_hash', 'string')
            ->addColumn('added_at', 'datetime')
            ->addColumn('added_at_time_zone', 'string')
            ->create();
    }
}
