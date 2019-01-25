<?php
declare(strict_types=1);

namespace src\app\data\NotificationEmail;

use Atlas\Mapper\RecordSet;

/**
 * @method NotificationEmailRecord offsetGet($offset)
 * @method NotificationEmailRecord appendNew(array $fields = [])
 * @method NotificationEmailRecord|null getOneBy(array $whereEquals)
 * @method NotificationEmailRecordSet getAllBy(array $whereEquals)
 * @method NotificationEmailRecord|null detachOneBy(array $whereEquals)
 * @method NotificationEmailRecordSet detachAllBy(array $whereEquals)
 * @method NotificationEmailRecordSet detachAll()
 * @method NotificationEmailRecordSet detachDeleted()
 */
class NotificationEmailRecordSet extends RecordSet
{
}
