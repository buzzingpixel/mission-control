<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateSshPublicKeysTable extends AbstractMigration
{
    public function change()
    {
        $this->table('ssh_public_keys', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => 1,
                'comment' => 'Determines active or archived',
            ])
            ->addColumn('title', 'string', [
                'comment' => 'The title',
            ])
            ->addColumn('slug', 'string', [
                'comment' => 'The slug (code should ensure uniqueness)',
            ])
            ->addColumn('key', 'text', [
                'comment' => 'The Public Key',
            ])
            ->create();
    }
}
