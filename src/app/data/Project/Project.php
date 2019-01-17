<?php
declare(strict_types=1);

namespace src\app\data\Project;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method ProjectTable getTable()
 * @method ProjectRelationships getRelationships()
 * @method ProjectRecord|null fetchRecord($primaryVal, array $with = [])
 * @method ProjectRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method ProjectRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method ProjectRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method ProjectRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method ProjectRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method ProjectSelect select(array $whereEquals = [])
 * @method ProjectRecord newRecord(array $fields = [])
 * @method ProjectRecord[] newRecords(array $fieldSets)
 * @method ProjectRecordSet newRecordSet(array $records = [])
 * @method ProjectRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method ProjectRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Project extends Mapper
{
}
