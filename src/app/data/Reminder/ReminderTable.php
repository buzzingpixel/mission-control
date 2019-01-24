<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\Reminder;

use Atlas\Table\Table;

/**
 * @method ReminderRow|null fetchRow($primaryVal)
 * @method ReminderRow[] fetchRows(array $primaryVals)
 * @method ReminderTableSelect select(array $whereEquals = [])
 * @method ReminderRow newRow(array $cols = [])
 * @method ReminderRow newSelectedRow(array $cols)
 */
class ReminderTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'reminders';

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
        'message' => [
            'name' => 'message',
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
        'is_active',
        'title',
        'slug',
        'message',
        'added_at',
        'added_at_time_zone',
    ];

    const COLUMN_DEFAULTS = [
        'guid' => null,
        'project_guid' => 'NULL',
        'is_active' => 1,
        'title' => null,
        'slug' => null,
        'message' => null,
        'added_at' => null,
        'added_at_time_zone' => null,
    ];

    const PRIMARY_KEY = [
        'guid',
    ];

    const AUTOINC_COLUMN = null;

    const AUTOINC_SEQUENCE = null;
}
