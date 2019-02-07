<?php
declare(strict_types=1);

namespace src\app\data\SshKey;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method SshKeyTable getTable()
 * @method SshKeyRelationships getRelationships()
 * @method SshKeyRecord|null fetchRecord($primaryVal, array $with = [])
 * @method SshKeyRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method SshKeyRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method SshKeyRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method SshKeyRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method SshKeyRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method SshKeySelect select(array $whereEquals = [])
 * @method SshKeyRecord newRecord(array $fields = [])
 * @method SshKeyRecord[] newRecords(array $fieldSets)
 * @method SshKeyRecordSet newRecordSet(array $records = [])
 * @method SshKeyRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method SshKeyRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class SshKey extends Mapper
{
}
