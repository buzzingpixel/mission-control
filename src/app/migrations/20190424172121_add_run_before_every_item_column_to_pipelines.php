<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddRunBeforeEveryItemColumnToPipelines extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('pipelines');

        $table->addColumn('run_before_every_item', 'text', [
            'after' => 'secret_id',
            'comment' => 'Runs before every item script',
        ])
        ->update();
    }
}
