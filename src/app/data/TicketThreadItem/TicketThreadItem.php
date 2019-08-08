<?php
declare(strict_types=1);

namespace src\app\data\TicketThreadItem;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method TicketThreadItemTable getTable()
 * @method TicketThreadItemRelationships getRelationships()
 * @method TicketThreadItemRecord|null fetchRecord($primaryVal, array $with = [])
 * @method TicketThreadItemRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method TicketThreadItemRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method TicketThreadItemRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method TicketThreadItemRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method TicketThreadItemRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method TicketThreadItemSelect select(array $whereEquals = [])
 * @method TicketThreadItemRecord newRecord(array $fields = [])
 * @method TicketThreadItemRecord[] newRecords(array $fieldSets)
 * @method TicketThreadItemRecordSet newRecordSet(array $records = [])
 * @method TicketThreadItemRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method TicketThreadItemRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class TicketThreadItem extends Mapper
{
}
