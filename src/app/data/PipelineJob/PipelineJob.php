<?php
declare(strict_types=1);

namespace src\app\data\PipelineJob;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method PipelineJobTable getTable()
 * @method PipelineJobRelationships getRelationships()
 * @method PipelineJobRecord|null fetchRecord($primaryVal, array $with = [])
 * @method PipelineJobRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method PipelineJobRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method PipelineJobRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method PipelineJobRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method PipelineJobRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method PipelineJobSelect select(array $whereEquals = [])
 * @method PipelineJobRecord newRecord(array $fields = [])
 * @method PipelineJobRecord[] newRecords(array $fieldSets)
 * @method PipelineJobRecordSet newRecordSet(array $records = [])
 * @method PipelineJobRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method PipelineJobRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class PipelineJob extends Mapper
{
}
