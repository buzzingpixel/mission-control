<?php
declare(strict_types=1);

namespace src\app\data\UserSession;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method UserSessionTable getTable()
 * @method UserSessionRelationships getRelationships()
 * @method UserSessionRecord|null fetchRecord($primaryVal, array $with = [])
 * @method UserSessionRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method UserSessionRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method UserSessionRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method UserSessionRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method UserSessionRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method UserSessionSelect select(array $whereEquals = [])
 * @method UserSessionRecord newRecord(array $fields = [])
 * @method UserSessionRecord[] newRecords(array $fieldSets)
 * @method UserSessionRecordSet newRecordSet(array $records = [])
 * @method UserSessionRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method UserSessionRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class UserSession extends Mapper
{
}
