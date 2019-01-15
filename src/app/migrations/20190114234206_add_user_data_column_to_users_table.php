<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class AddUserDataColumnToUsersTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');

        $table->addColumn('user_data', 'text', [
            'after' => 'password_hash',
            'null' => true,
        ]);

        $table->save();
    }
}
