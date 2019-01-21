<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\User;

use Atlas\Table\Table;

/**
 * @method UserRow|null fetchRow($primaryVal)
 * @method UserRow[] fetchRows(array $primaryVals)
 * @method UserTableSelect select(array $whereEquals = [])
 * @method UserRow newRow(array $cols = [])
 * @method UserRow newSelectedRow(array $cols)
 */
class UserTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'users';

    const COLUMNS = [
        'id' => [
            'name' => 'id',
            'type' => 'int',
            'size' => 10,
            'scale' => 0,
            'notnull' => true,
            'default' => null,
            'autoinc' => true,
            'primary' => true,
            'options' => null,
        ],
        'guid' => [
            'name' => 'guid',
            'type' => 'varchar',
            'size' => 255,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'email_address' => [
            'name' => 'email_address',
            'type' => 'text',
            'size' => 65535,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'password_hash' => [
            'name' => 'password_hash',
            'type' => 'varchar',
            'size' => 255,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'user_data' => [
            'name' => 'user_data',
            'type' => 'text',
            'size' => 65535,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
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
        'timezone' => [
            'name' => 'timezone',
            'type' => 'varchar',
            'size' => 255,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
    ];

    const COLUMN_NAMES = [
        'id',
        'guid',
        'email_address',
        'password_hash',
        'user_data',
        'added_at',
        'added_at_time_zone',
        'timezone',
    ];

    const COLUMN_DEFAULTS = [
        'id' => null,
        'guid' => null,
        'email_address' => null,
        'password_hash' => null,
        'user_data' => 'NULL',
        'added_at' => null,
        'added_at_time_zone' => null,
        'timezone' => 'NULL',
    ];

    const PRIMARY_KEY = [
        'id',
    ];

    const AUTOINC_COLUMN = 'id';

    const AUTOINC_SEQUENCE = null;
}
