<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\NotificationEmail;

use Atlas\Table\Table;

/**
 * @method NotificationEmailRow|null fetchRow($primaryVal)
 * @method NotificationEmailRow[] fetchRows(array $primaryVals)
 * @method NotificationEmailTableSelect select(array $whereEquals = [])
 * @method NotificationEmailRow newRow(array $cols = [])
 * @method NotificationEmailRow newSelectedRow(array $cols)
 */
class NotificationEmailTable extends Table
{
    const DRIVER = 'mysql';

    const NAME = 'notification_emails';

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
    ];

    const COLUMN_NAMES = [
        'guid',
        'is_active',
        'email_address',
    ];

    const COLUMN_DEFAULTS = [
        'guid' => null,
        'is_active' => 1,
        'email_address' => null,
    ];

    const PRIMARY_KEY = [
        'guid',
    ];

    const AUTOINC_COLUMN = null;

    const AUTOINC_SEQUENCE = null;
}
