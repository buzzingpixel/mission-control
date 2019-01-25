<?php
declare(strict_types=1);

namespace src\app\data\NotificationEmail;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method NotificationEmailTable getTable()
 * @method NotificationEmailRelationships getRelationships()
 * @method NotificationEmailRecord|null fetchRecord($primaryVal, array $with = [])
 * @method NotificationEmailRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method NotificationEmailRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method NotificationEmailRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method NotificationEmailRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method NotificationEmailRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method NotificationEmailSelect select(array $whereEquals = [])
 * @method NotificationEmailRecord newRecord(array $fields = [])
 * @method NotificationEmailRecord[] newRecords(array $fieldSets)
 * @method NotificationEmailRecordSet newRecordSet(array $records = [])
 * @method NotificationEmailRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method NotificationEmailRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class NotificationEmail extends Mapper
{
}
