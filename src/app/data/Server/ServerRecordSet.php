<?php
declare(strict_types=1);

namespace src\app\data\Server;

use Atlas\Mapper\RecordSet;

/**
 * @method ServerRecord offsetGet($offset)
 * @method ServerRecord appendNew(array $fields = [])
 * @method ServerRecord|null getOneBy(array $whereEquals)
 * @method ServerRecordSet getAllBy(array $whereEquals)
 * @method ServerRecord|null detachOneBy(array $whereEquals)
 * @method ServerRecordSet detachAllBy(array $whereEquals)
 * @method ServerRecordSet detachAll()
 * @method ServerRecordSet detachDeleted()
 */
class ServerRecordSet extends RecordSet
{
}
