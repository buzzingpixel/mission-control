<?php
declare(strict_types=1);

namespace src\app\data\Migration;

use Atlas\Mapper\RecordSet;

/**
 * @method MigrationRecord offsetGet($offset)
 * @method MigrationRecord appendNew(array $fields = [])
 * @method MigrationRecord|null getOneBy(array $whereEquals)
 * @method MigrationRecordSet getAllBy(array $whereEquals)
 * @method MigrationRecord|null detachOneBy(array $whereEquals)
 * @method MigrationRecordSet detachAllBy(array $whereEquals)
 * @method MigrationRecordSet detachAll()
 * @method MigrationRecordSet detachDeleted()
 */
class MigrationRecordSet extends RecordSet
{
}
