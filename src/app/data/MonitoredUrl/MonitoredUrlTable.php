<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\MonitoredUrl;

use Atlas\Table\Table;

/**
 * @method MonitoredUrlRow|null fetchRow($primaryVal)
 * @method MonitoredUrlRow[] fetchRows(array $primaryVals)
 * @method MonitoredUrlTableSelect select(array $whereEquals = [])
 * @method MonitoredUrlRow newRow(array $cols = [])
 * @method MonitoredUrlRow newSelectedRow(array $cols)
 */
class MonitoredUrlTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'monitored_urls';

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
        'url' => [
            'name' => 'url',
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
        'checked_at' => [
            'name' => 'checked_at',
            'type' => 'datetime',
            'size' => null,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'checked_at_time_zone' => [
            'name' => 'checked_at_time_zone',
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
        'url',
        'pending_error',
        'has_error',
        'checked_at',
        'checked_at_time_zone',
        'added_at',
        'added_at_time_zone',
    ];

    const COLUMN_DEFAULTS = [
        'guid' => null,
        'project_guid' => null,
        'is_active' => 1,
        'title' => null,
        'slug' => null,
        'url' => null,
        'pending_error' => 0,
        'has_error' => 0,
        'checked_at' => null,
        'checked_at_time_zone' => null,
        'added_at' => null,
        'added_at_time_zone' => null,
    ];

    const PRIMARY_KEY = [
        'guid',
    ];

    const AUTOINC_COLUMN = null;

    const AUTOINC_SEQUENCE = null;
}
