<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddIsAdminUserColumn extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('users');

        $table->addColumn('is_admin', 'boolean', ['default' => 0]);

        $table->save();
    }
}
