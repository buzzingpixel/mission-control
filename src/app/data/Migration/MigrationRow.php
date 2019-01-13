<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\Migration;

use Atlas\Table\Row;

/**
 * @property mixed $version bigint(19,0) NOT NULL
 * @property mixed $migration_name varchar(100)
 * @property mixed $start_time timestamp
 * @property mixed $end_time timestamp
 * @property mixed $breakpoint tinyint(3,0) NOT NULL
 */
class MigrationRow extends Row
{
    protected $cols = [
        'version' => null,
        'migration_name' => 'NULL',
        'start_time' => 'NULL',
        'end_time' => 'NULL',
        'breakpoint' => '0',
    ];
}
