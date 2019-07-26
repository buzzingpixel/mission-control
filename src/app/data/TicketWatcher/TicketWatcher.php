<?php
declare(strict_types=1);

namespace src\app\data\TicketWatcher;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method TicketWatcherTable getTable()
 * @method TicketWatcherRelationships getRelationships()
 * @method TicketWatcherRecord|null fetchRecord($primaryVal, array $with = [])
 * @method TicketWatcherRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method TicketWatcherRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method TicketWatcherRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method TicketWatcherRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method TicketWatcherRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method TicketWatcherSelect select(array $whereEquals = [])
 * @method TicketWatcherRecord newRecord(array $fields = [])
 * @method TicketWatcherRecord[] newRecords(array $fieldSets)
 * @method TicketWatcherRecordSet newRecordSet(array $records = [])
 * @method TicketWatcherRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method TicketWatcherRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class TicketWatcher extends Mapper
{
}
