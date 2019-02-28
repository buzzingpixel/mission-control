<?php
declare(strict_types=1);

namespace src\app\data\Pipeline;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method PipelineTable getTable()
 * @method PipelineRelationships getRelationships()
 * @method PipelineRecord|null fetchRecord($primaryVal, array $with = [])
 * @method PipelineRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method PipelineRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method PipelineRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method PipelineRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method PipelineRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method PipelineSelect select(array $whereEquals = [])
 * @method PipelineRecord newRecord(array $fields = [])
 * @method PipelineRecord[] newRecords(array $fieldSets)
 * @method PipelineRecordSet newRecordSet(array $records = [])
 * @method PipelineRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method PipelineRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Pipeline extends Mapper
{
}
