<?php
declare(strict_types=1);

namespace src\app\data\TicketWatcher;

use Atlas\Mapper\RecordSet;

/**
 * @method TicketWatcherRecord offsetGet($offset)
 * @method TicketWatcherRecord appendNew(array $fields = [])
 * @method TicketWatcherRecord|null getOneBy(array $whereEquals)
 * @method TicketWatcherRecordSet getAllBy(array $whereEquals)
 * @method TicketWatcherRecord|null detachOneBy(array $whereEquals)
 * @method TicketWatcherRecordSet detachAllBy(array $whereEquals)
 * @method TicketWatcherRecordSet detachAll()
 * @method TicketWatcherRecordSet detachDeleted()
 */
class TicketWatcherRecordSet extends RecordSet
{
}
