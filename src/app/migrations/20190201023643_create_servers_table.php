<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateServersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('servers', [
                'id' => false,
                'primary_key' => [
                    'guid'
                ]
            ])
            ->addColumn('guid', 'binary', [
                'limit' => 16,
                'comment' => 'UUID generated in code and stored as binary',
            ])
            ->addColumn('project_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'Associated project UUID stored as binary',
            ])
            ->addColumn('remote_service_adapter', 'string', [
                'null' => true,
                'comment' => 'The related remote service adapter (like DigitalOcean)',
            ])
            ->addColumn('remote_id', 'string', [
                'null' => true,
                'comment' => 'The related remote service adapter ID',
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
            ->addColumn('address', 'string', [
                'comment' => 'The server address or IP',
            ])
            ->addColumn('ssh_port', 'string', [
                'comment' => 'The server ssh port',
            ])
            ->addColumn('ssh_public_key_guid', 'binary', [
                'null' => true,
                'limit' => 16,
                'comment' => 'The SSH key to use when connecting to the server',
            ])
            ->addColumn('ssh_user_name', 'string', [
                'comment' => 'The server ssh user name',
            ])
            ->create();
    }
}
