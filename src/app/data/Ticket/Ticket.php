<?php
declare(strict_types=1);

namespace src\app\data\Ticket;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method TicketTable getTable()
 * @method TicketRelationships getRelationships()
 * @method TicketRecord|null fetchRecord($primaryVal, array $with = [])
 * @method TicketRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method TicketRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method TicketRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method TicketRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method TicketRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method TicketSelect select(array $whereEquals = [])
 * @method TicketRecord newRecord(array $fields = [])
 * @method TicketRecord[] newRecords(array $fieldSets)
 * @method TicketRecordSet newRecordSet(array $records = [])
 * @method TicketRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method TicketRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Ticket extends Mapper
{
}
