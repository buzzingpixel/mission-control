<?php
declare(strict_types=1);

namespace src\app\data\SshKey;

use Atlas\Mapper\RecordSet;

/**
 * @method SshKeyRecord offsetGet($offset)
 * @method SshKeyRecord appendNew(array $fields = [])
 * @method SshKeyRecord|null getOneBy(array $whereEquals)
 * @method SshKeyRecordSet getAllBy(array $whereEquals)
 * @method SshKeyRecord|null detachOneBy(array $whereEquals)
 * @method SshKeyRecordSet detachAllBy(array $whereEquals)
 * @method SshKeyRecordSet detachAll()
 * @method SshKeyRecordSet detachDeleted()
 */
class SshKeyRecordSet extends RecordSet
{
}
