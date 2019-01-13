<?php
declare(strict_types=1);

namespace src\app\data\ActionQueueItem;

use Atlas\Mapper\RecordSet;

/**
 * @method ActionQueueItemRecord offsetGet($offset)
 * @method ActionQueueItemRecord appendNew(array $fields = [])
 * @method ActionQueueItemRecord|null getOneBy(array $whereEquals)
 * @method ActionQueueItemRecordSet getAllBy(array $whereEquals)
 * @method ActionQueueItemRecord|null detachOneBy(array $whereEquals)
 * @method ActionQueueItemRecordSet detachAllBy(array $whereEquals)
 * @method ActionQueueItemRecordSet detachAll()
 * @method ActionQueueItemRecordSet detachDeleted()
 */
class ActionQueueItemRecordSet extends RecordSet
{
}
