<?php
declare(strict_types=1);

namespace src\app\data\Ping;

use Atlas\Mapper\RecordSet;

/**
 * @method PingRecord offsetGet($offset)
 * @method PingRecord appendNew(array $fields = [])
 * @method PingRecord|null getOneBy(array $whereEquals)
 * @method PingRecordSet getAllBy(array $whereEquals)
 * @method PingRecord|null detachOneBy(array $whereEquals)
 * @method PingRecordSet detachAllBy(array $whereEquals)
 * @method PingRecordSet detachAll()
 * @method PingRecordSet detachDeleted()
 */
class PingRecordSet extends RecordSet
{
}
