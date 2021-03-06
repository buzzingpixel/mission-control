<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class ChangePipelineJobItemLogContentToLongText extends AbstractMigration
{
    public function change() : void
    {
        $this->table('pipeline_job_items')
            ->changeColumn(
                'log_content',
                MysqlAdapter::PHINX_TYPE_TEXT,
                [
                    'limit' => MysqlAdapter::TEXT_LONG,
                    'comment' => 'Log content',
                ]
            )
            ->save();
    }
}
