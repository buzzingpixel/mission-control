<?php
declare(strict_types=1);

namespace src\app\data\Ticket;

use Atlas\Mapper\Record;

/**
 * @method TicketRow getRow()
 */
class TicketRecord extends Record
{
    use TicketFields;
}
