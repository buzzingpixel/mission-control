<?php
declare(strict_types=1);

namespace src\app\data\Reminder;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method ReminderTable getTable()
 * @method ReminderRelationships getRelationships()
 * @method ReminderRecord|null fetchRecord($primaryVal, array $with = [])
 * @method ReminderRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method ReminderRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method ReminderRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method ReminderRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method ReminderRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method ReminderSelect select(array $whereEquals = [])
 * @method ReminderRecord newRecord(array $fields = [])
 * @method ReminderRecord[] newRecords(array $fieldSets)
 * @method ReminderRecordSet newRecordSet(array $records = [])
 * @method ReminderRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method ReminderRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Reminder extends Mapper
{
}
