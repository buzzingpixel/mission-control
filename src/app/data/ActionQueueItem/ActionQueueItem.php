<?php
declare(strict_types=1);

namespace src\app\data\ActionQueueItem;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method ActionQueueItemTable getTable()
 * @method ActionQueueItemRelationships getRelationships()
 * @method ActionQueueItemRecord|null fetchRecord($primaryVal, array $with = [])
 * @method ActionQueueItemRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method ActionQueueItemRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method ActionQueueItemRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method ActionQueueItemRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method ActionQueueItemRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method ActionQueueItemSelect select(array $whereEquals = [])
 * @method ActionQueueItemRecord newRecord(array $fields = [])
 * @method ActionQueueItemRecord[] newRecords(array $fieldSets)
 * @method ActionQueueItemRecordSet newRecordSet(array $records = [])
 * @method ActionQueueItemRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method ActionQueueItemRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class ActionQueueItem extends Mapper
{
}
