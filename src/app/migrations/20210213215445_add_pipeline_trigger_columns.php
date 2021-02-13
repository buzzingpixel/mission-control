<?php

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddPipelineTriggerColumns extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('pipelines');

        $table->addColumn(
            'enable_webhook',
            'boolean',
            [
                'after' => 'is_active',
                'default' => 0,
            ]
        );

        $table->addColumn(
            'webhook_check_for_branch',
            'string',
            [
                'after' => 'enable_webhook',
                'null' => true,
                'default' => '',
            ]
        );

        $table->update();
    }
}
