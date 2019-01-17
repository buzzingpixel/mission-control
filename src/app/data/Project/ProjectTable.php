<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\Project;

use Atlas\Table\Table;

/**
 * @method ProjectRow|null fetchRow($primaryVal)
 * @method ProjectRow[] fetchRows(array $primaryVals)
 * @method ProjectTableSelect select(array $whereEquals = [])
 * @method ProjectRow newRow(array $cols = [])
 * @method ProjectRow newSelectedRow(array $cols)
 */
class ProjectTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'projects';

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
        'is_active' => [
            'name' => 'is_active',
            'type' => 'tinyint',
            'size' => 3,
            'scale' => 0,
            'notnull' => true,
            'default' => '1',
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
        'description' => [
            'name' => 'description',
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
        'id',
        'guid',
        'is_active',
        'title',
        'slug',
        'description',
        'added_at',
        'added_at_time_zone',
    ];

    const COLUMN_DEFAULTS = [
        'id' => null,
        'guid' => null,
        'is_active' => '1',
        'title' => null,
        'slug' => null,
        'description' => null,
        'added_at' => null,
        'added_at_time_zone' => null,
    ];

    const PRIMARY_KEY = [
        'id',
    ];

    const AUTOINC_COLUMN = 'id';

    const AUTOINC_SEQUENCE = null;
}
