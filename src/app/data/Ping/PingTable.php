<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\Ping;

use Atlas\Table\Table;

/**
 * @method PingRow|null fetchRow($primaryVal)
 * @method PingRow[] fetchRows(array $primaryVals)
 * @method PingTableSelect select(array $whereEquals = [])
 * @method PingRow newRow(array $cols = [])
 * @method PingRow newSelectedRow(array $cols)
 */
class PingTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'pings';

    const COLUMNS = [
        'guid' => [
            'name' => 'guid',
            'type' => 'binary',
            'size' => 16,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => true,
            'options' => null,
        ],
        'project_guid' => [
            'name' => 'project_guid',
            'type' => 'binary',
            'size' => 16,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'ping_id' => [
            'name' => 'ping_id',
            'type' => 'varchar',
            'size' => 16,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'is_active' => [
            'name' => 'is_active',
            'type' => 'tinyint',
            'size' => 3,
            'scale' => 0,
            'notnull' => true,
            'default' => 1,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'title' => [
            'name' => 'title',
            'type' => 'varchar',
            'size' => 255,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'slug' => [
            'name' => 'slug',
            'type' => 'varchar',
            'size' => 255,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'pending_error' => [
            'name' => 'pending_error',
            'type' => 'tinyint',
            'size' => 3,
            'scale' => 0,
            'notnull' => true,
            'default' => 0,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'has_error' => [
            'name' => 'has_error',
            'type' => 'tinyint',
            'size' => 3,
            'scale' => 0,
            'notnull' => true,
            'default' => 0,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'expect_every' => [
            'name' => 'expect_every',
            'type' => 'bigint unsigned',
            'size' => 20,
            'scale' => 0,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'warn_after' => [
            'name' => 'warn_after',
            'type' => 'bigint unsigned',
            'size' => 20,
            'scale' => 0,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'last_ping_at' => [
            'name' => 'last_ping_at',
            'type' => 'datetime',
            'size' => null,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'last_ping_at_time_zone' => [
            'name' => 'last_ping_at_time_zone',
            'type' => 'varchar',
            'size' => 255,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'added_at' => [
            'name' => 'added_at',
            'type' => 'datetime',
            'size' => null,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'added_at_time_zone' => [
            'name' => 'added_at_time_zone',
            'type' => 'varchar',
            'size' => 255,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
    ];

    const COLUMN_NAMES = [
        'guid',
        'project_guid',
        'ping_id',
        'is_active',
        'title',
        'slug',
        'pending_error',
        'has_error',
        'expect_every',
        'warn_after',
        'last_ping_at',
        'last_ping_at_time_zone',
        'added_at',
        'added_at_time_zone',
    ];

    const COLUMN_DEFAULTS = [
        'guid' => null,
        'project_guid' => 'NULL',
        'ping_id' => null,
        'is_active' => 1,
        'title' => null,
        'slug' => null,
        'pending_error' => 0,
        'has_error' => 0,
        'expect_every' => null,
        'warn_after' => null,
        'last_ping_at' => null,
        'last_ping_at_time_zone' => null,
        'added_at' => null,
        'added_at_time_zone' => null,
    ];

    const PRIMARY_KEY = [
        'guid',
    ];

    const AUTOINC_COLUMN = null;

    const AUTOINC_SEQUENCE = null;
}
