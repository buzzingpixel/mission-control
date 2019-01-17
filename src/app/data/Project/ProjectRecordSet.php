<?php
declare(strict_types=1);

namespace src\app\data\Project;

use Atlas\Mapper\RecordSet;

/**
 * @method ProjectRecord offsetGet($offset)
 * @method ProjectRecord appendNew(array $fields = [])
 * @method ProjectRecord|null getOneBy(array $whereEquals)
 * @method ProjectRecordSet getAllBy(array $whereEquals)
 * @method ProjectRecord|null detachOneBy(array $whereEquals)
 * @method ProjectRecordSet detachAllBy(array $whereEquals)
 * @method ProjectRecordSet detachAll()
 * @method ProjectRecordSet detachDeleted()
 */
class ProjectRecordSet extends RecordSet
{
}
