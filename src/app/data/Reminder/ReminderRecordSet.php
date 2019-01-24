<?php
declare(strict_types=1);

namespace src\app\data\Reminder;

use Atlas\Mapper\RecordSet;

/**
 * @method ReminderRecord offsetGet($offset)
 * @method ReminderRecord appendNew(array $fields = [])
 * @method ReminderRecord|null getOneBy(array $whereEquals)
 * @method ReminderRecordSet getAllBy(array $whereEquals)
 * @method ReminderRecord|null detachOneBy(array $whereEquals)
 * @method ReminderRecordSet detachAllBy(array $whereEquals)
 * @method ReminderRecordSet detachAll()
 * @method ReminderRecordSet detachDeleted()
 */
class ReminderRecordSet extends RecordSet
{
}
