<?php
declare(strict_types=1);

namespace src\app\data\Migration;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method MigrationTable getTable()
 * @method MigrationRelationships getRelationships()
 * @method MigrationRecord|null fetchRecord($primaryVal, array $with = [])
 * @method MigrationRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method MigrationRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method MigrationRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method MigrationRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method MigrationRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method MigrationSelect select(array $whereEquals = [])
 * @method MigrationRecord newRecord(array $fields = [])
 * @method MigrationRecord[] newRecords(array $fieldSets)
 * @method MigrationRecordSet newRecordSet(array $records = [])
 * @method MigrationRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method MigrationRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Migration extends Mapper
{
}
