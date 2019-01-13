<?php
declare(strict_types=1);

namespace src\app\data\ScheduleTracking;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method ScheduleTrackingTable getTable()
 * @method ScheduleTrackingRelationships getRelationships()
 * @method ScheduleTrackingRecord|null fetchRecord($primaryVal, array $with = [])
 * @method ScheduleTrackingRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method ScheduleTrackingRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method ScheduleTrackingRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method ScheduleTrackingRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method ScheduleTrackingRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method ScheduleTrackingSelect select(array $whereEquals = [])
 * @method ScheduleTrackingRecord newRecord(array $fields = [])
 * @method ScheduleTrackingRecord[] newRecords(array $fieldSets)
 * @method ScheduleTrackingRecordSet newRecordSet(array $records = [])
 * @method ScheduleTrackingRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method ScheduleTrackingRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class ScheduleTracking extends Mapper
{
}
