<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddIsRunningAssumeDeadAfterColsToBatchTable extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('action_queue_batch');

        $table->addColumn('is_running', 'boolean', [
            'after' => 'has_started',
            'default' => 0,
            'comment' => 'Whether this batch currently has a worker running it',
        ])
        ->update();

        $table->addColumn('assume_dead_after', 'datetime', [
            'after' => 'is_running',
            'comment' => 'The datetime representation of when the batch is to be assumed dead and picked up by another worker',
        ])
        ->update();

        $table->addColumn('assume_dead_after_time_zone', 'string', [
            'after' => 'assume_dead_after',
            'comment' => 'The timezone assume_dead_after was set with',
        ])
        ->update();

        $table->addColumn('initial_assume_dead_after', 'datetime', [
            'after' => 'assume_dead_after_time_zone',
            'comment' => 'The datetime representation of the initial value for when the batch is to be assumed dead and picked up by another worker',
        ])
        ->update();

        $table->addColumn('initial_assume_dead_after_time_zone', 'string', [
            'after' => 'initial_assume_dead_after',
            'comment' => 'The timezone initial_assume_dead_after was set with',
        ])
        ->update();
    }
}
