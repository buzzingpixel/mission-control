<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateUserTimezoneColumn extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('users');

        $table->addColumn('timezone', 'string', ['null' => true]);

        $table->save();
    }
}
