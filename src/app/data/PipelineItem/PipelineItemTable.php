<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\PipelineItem;

use Atlas\Table\Table;

/**
 * @method PipelineItemRow|null fetchRow($primaryVal)
 * @method PipelineItemRow[] fetchRows(array $primaryVals)
 * @method PipelineItemTableSelect select(array $whereEquals = [])
 * @method PipelineItemRow newRow(array $cols = [])
 * @method PipelineItemRow newSelectedRow(array $cols)
 */
class PipelineItemTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'pipeline_items';

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
        'pipeline_guid' => [
            'name' => 'pipeline_guid',
            'type' => 'binary',
            'size' => 16,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'order' => [
            'name' => 'order',
            'type' => 'int',
            'size' => 10,
            'scale' => 0,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'script' => [
            'name' => 'script',
            'type' => 'text',
            'size' => 65535,
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
        'pipeline_guid',
        'order',
        'script',
    ];

    const COLUMN_DEFAULTS = [
        'guid' => null,
        'pipeline_guid' => 'NULL',
        'order' => null,
        'script' => null,
    ];

    const PRIMARY_KEY = [
        'guid',
    ];

    const AUTOINC_COLUMN = null;

    const AUTOINC_SEQUENCE = null;
}
