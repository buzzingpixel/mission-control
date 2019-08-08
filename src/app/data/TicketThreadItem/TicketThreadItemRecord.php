<?php
declare(strict_types=1);

namespace src\app\data\TicketThreadItem;

use Atlas\Mapper\Record;

/**
 * @method TicketThreadItemRow getRow()
 */
class TicketThreadItemRecord extends Record
{
    use TicketThreadItemFields;
}
