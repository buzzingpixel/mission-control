<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\Ticket;

use Atlas\Table\Table;

/**
 * @method TicketRow|null fetchRow($primaryVal)
 * @method TicketRow[] fetchRows(array $primaryVals)
 * @method TicketTableSelect select(array $whereEquals = [])
 * @method TicketRow newRow(array $cols = [])
 * @method TicketRow newSelectedRow(array $cols)
 */
class TicketTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'tickets';

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
        'created_by_user_guid' => [
            'name' => 'created_by_user_guid',
            'type' => 'binary',
            'size' => 16,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'assigned_to_user_guid' => [
            'name' => 'assigned_to_user_guid',
            'type' => 'binary',
            'size' => 16,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'title' => [
            'name' => 'title',
            'type' => 'text',
            'size' => 65535,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'content' => [
            'name' => 'content',
            'type' => 'text',
            'size' => 65535,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'status' => [
            'name' => 'status',
            'type' => 'enum',
            'size' => 11,
            'scale' => null,
            'notnull' => true,
            'default' => '\'new\'',
            'autoinc' => false,
            'primary' => false,
            'options' => 
  array (
    0 => '\'new\'',
    1 => '\'in_progress\'',
    2 => '\'on_hold\'',
    3 => '\'resolved\'',
  ),
        ],
        'watchers' => [
            'name' => 'watchers',
            'type' => 'text',
            'size' => 65535,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'added_at_utc' => [
            'name' => 'added_at_utc',
            'type' => 'datetime',
            'size' => null,
            'scale' => null,
            'notnull' => true,
            'default' => null,
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
        'resolved_at_utc' => [
            'name' => 'resolved_at_utc',
            'type' => 'datetime',
            'size' => null,
            'scale' => null,
            'notnull' => false,
            'default' => 'NULL',
            'autoinc' => false,
            'primary' => false,
            'options' => null,
        ],
    ];

    const COLUMN_NAMES = [
        'guid',
        'created_by_user_guid',
        'assigned_to_user_guid',
        'title',
        'content',
        'status',
        'watchers',
        'added_at_utc',
        'resolved_at_utc',
    ];

    const COLUMN_DEFAULTS = [
        'guid' => null,
        'created_by_user_guid' => 'NULL',
        'assigned_to_user_guid' => 'NULL',
        'title' => 'NULL',
        'content' => 'NULL',
        'status' => '\'new\'',
        'watchers' => 'NULL',
        'added_at_utc' => null,
        'resolved_at_utc' => 'NULL',
    ];

    const PRIMARY_KEY = [
        'guid',
    ];

    const AUTOINC_COLUMN = null;

    const AUTOINC_SEQUENCE = null;
}
