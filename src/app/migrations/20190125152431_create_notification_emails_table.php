<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use Phinx\Migration\AbstractMigration;

class CreateNotificationEmailsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('notification_emails', [
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
                'comment' => 'Determines whether this email address is currently receiving notifications',
            ])
            ->addColumn('email_address', 'text', [
                'comment' => 'Address to send notification to',
            ])
            ->create();
    }
}
