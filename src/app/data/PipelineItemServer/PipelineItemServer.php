<?php
declare(strict_types=1);

namespace src\app\data\PipelineItemServer;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method PipelineItemServerTable getTable()
 * @method PipelineItemServerRelationships getRelationships()
 * @method PipelineItemServerRecord|null fetchRecord($primaryVal, array $with = [])
 * @method PipelineItemServerRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method PipelineItemServerRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method PipelineItemServerRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method PipelineItemServerRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method PipelineItemServerRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method PipelineItemServerSelect select(array $whereEquals = [])
 * @method PipelineItemServerRecord newRecord(array $fields = [])
 * @method PipelineItemServerRecord[] newRecords(array $fieldSets)
 * @method PipelineItemServerRecordSet newRecordSet(array $records = [])
 * @method PipelineItemServerRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method PipelineItemServerRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class PipelineItemServer extends Mapper
{
}
