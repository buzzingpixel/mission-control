<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateUserTimezoneColumn extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');

        $table->addColumn('timezone', 'string', ['null' => true]);

        $table->save();
    }
}
