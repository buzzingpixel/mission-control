<?php
declare(strict_types=1);

namespace src\app\data\ActionQueueBatch;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method ActionQueueBatchTable getTable()
 * @method ActionQueueBatchRelationships getRelationships()
 * @method ActionQueueBatchRecord|null fetchRecord($primaryVal, array $with = [])
 * @method ActionQueueBatchRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method ActionQueueBatchRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method ActionQueueBatchRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method ActionQueueBatchRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method ActionQueueBatchRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method ActionQueueBatchSelect select(array $whereEquals = [])
 * @method ActionQueueBatchRecord newRecord(array $fields = [])
 * @method ActionQueueBatchRecord[] newRecords(array $fieldSets)
 * @method ActionQueueBatchRecordSet newRecordSet(array $records = [])
 * @method ActionQueueBatchRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method ActionQueueBatchRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class ActionQueueBatch extends Mapper
{
}
