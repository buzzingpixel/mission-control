<?php
declare(strict_types=1);

namespace src\app\data\TicketWatcher;

use Atlas\Mapper\Record;

/**
 * @method TicketWatcherRow getRow()
 */
class TicketWatcherRecord extends Record
{
    use TicketWatcherFields;
}
