<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\TicketWatcher;

use Atlas\Table\Row;

/**
 * @property mixed $guid binary(16) NOT NULL
 * @property mixed $ticket_guid binary(16)
 * @property mixed $user_guid binary(16)
 */
class TicketWatcherRow extends Row
{
    protected $cols = [
        'guid' => null,
        'ticket_guid' => 'NULL',
        'user_guid' => 'NULL',
    ];
}