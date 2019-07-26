<?php
declare(strict_types=1);

namespace src\app\data\Ticket;

use Atlas\Mapper\RecordSet;

/**
 * @method TicketRecord offsetGet($offset)
 * @method TicketRecord appendNew(array $fields = [])
 * @method TicketRecord|null getOneBy(array $whereEquals)
 * @method TicketRecordSet getAllBy(array $whereEquals)
 * @method TicketRecord|null detachOneBy(array $whereEquals)
 * @method TicketRecordSet detachAllBy(array $whereEquals)
 * @method TicketRecordSet detachAll()
 * @method TicketRecordSet detachDeleted()
 */
class TicketRecordSet extends RecordSet
{
}
