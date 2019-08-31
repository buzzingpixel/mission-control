<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateUserPasswordResetTokensTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('user_password_reset_tokens', [
            'id' => false,
            'primary_key' => ['guid'],
        ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('user_guid', 'binary', [
                'limit' => 16,
                'comment' => 'Associated user UUID stored as binary',
            ])
            ->addColumn('added_at', 'datetime', ['comment' => 'The datetime representation of when the password reset token was added'])
            ->addColumn('added_at_time_zone', 'string', ['comment' => 'The timezone added_at was set with'])
            ->create();
    }
}
