<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateUserTimezoneColumn extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('users');

        $table->addColumn('timezone', 'string', ['null' => true]);

        $table->save();
    }
}
