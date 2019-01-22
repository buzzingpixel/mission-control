<?php
declare(strict_types=1);

namespace src\app\data\Ping;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method PingTable getTable()
 * @method PingRelationships getRelationships()
 * @method PingRecord|null fetchRecord($primaryVal, array $with = [])
 * @method PingRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method PingRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method PingRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method PingRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method PingRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method PingSelect select(array $whereEquals = [])
 * @method PingRecord newRecord(array $fields = [])
 * @method PingRecord[] newRecords(array $fieldSets)
 * @method PingRecordSet newRecordSet(array $records = [])
 * @method PingRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method PingRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Ping extends Mapper
{
}
