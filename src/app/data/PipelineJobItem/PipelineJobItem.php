<?php
declare(strict_types=1);

namespace src\app\data\PipelineJobItem;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method PipelineJobItemTable getTable()
 * @method PipelineJobItemRelationships getRelationships()
 * @method PipelineJobItemRecord|null fetchRecord($primaryVal, array $with = [])
 * @method PipelineJobItemRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method PipelineJobItemRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method PipelineJobItemRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method PipelineJobItemRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method PipelineJobItemRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method PipelineJobItemSelect select(array $whereEquals = [])
 * @method PipelineJobItemRecord newRecord(array $fields = [])
 * @method PipelineJobItemRecord[] newRecords(array $fieldSets)
 * @method PipelineJobItemRecordSet newRecordSet(array $records = [])
 * @method PipelineJobItemRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method PipelineJobItemRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class PipelineJobItem extends Mapper
{
}
