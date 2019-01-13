<?php
declare(strict_types=1);

namespace src\app\data\UserSession;

use Atlas\Mapper\RecordSet;

/**
 * @method UserSessionRecord offsetGet($offset)
 * @method UserSessionRecord appendNew(array $fields = [])
 * @method UserSessionRecord|null getOneBy(array $whereEquals)
 * @method UserSessionRecordSet getAllBy(array $whereEquals)
 * @method UserSessionRecord|null detachOneBy(array $whereEquals)
 * @method UserSessionRecordSet detachAllBy(array $whereEquals)
 * @method UserSessionRecordSet detachAll()
 * @method UserSessionRecordSet detachDeleted()
 */
class UserSessionRecordSet extends RecordSet
{
}
