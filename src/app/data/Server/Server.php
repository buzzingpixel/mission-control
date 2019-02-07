<?php
declare(strict_types=1);

namespace src\app\data\Server;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method ServerTable getTable()
 * @method ServerRelationships getRelationships()
 * @method ServerRecord|null fetchRecord($primaryVal, array $with = [])
 * @method ServerRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method ServerRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method ServerRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method ServerRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method ServerRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method ServerSelect select(array $whereEquals = [])
 * @method ServerRecord newRecord(array $fields = [])
 * @method ServerRecord[] newRecords(array $fieldSets)
 * @method ServerRecordSet newRecordSet(array $records = [])
 * @method ServerRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method ServerRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Server extends Mapper
{
}
