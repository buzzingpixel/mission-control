<?php
declare(strict_types=1);

namespace src\app\data\PipelineItem;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method PipelineItemTable getTable()
 * @method PipelineItemRelationships getRelationships()
 * @method PipelineItemRecord|null fetchRecord($primaryVal, array $with = [])
 * @method PipelineItemRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method PipelineItemRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method PipelineItemRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method PipelineItemRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method PipelineItemRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method PipelineItemSelect select(array $whereEquals = [])
 * @method PipelineItemRecord newRecord(array $fields = [])
 * @method PipelineItemRecord[] newRecords(array $fieldSets)
 * @method PipelineItemRecordSet newRecordSet(array $records = [])
 * @method PipelineItemRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method PipelineItemRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class PipelineItem extends Mapper
{
}
