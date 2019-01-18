<?php
declare(strict_types=1);

namespace src\app\data\MonitoredUrlIncident;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method MonitoredUrlIncidentTable getTable()
 * @method MonitoredUrlIncidentRelationships getRelationships()
 * @method MonitoredUrlIncidentRecord|null fetchRecord($primaryVal, array $with = [])
 * @method MonitoredUrlIncidentRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method MonitoredUrlIncidentRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method MonitoredUrlIncidentRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method MonitoredUrlIncidentRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method MonitoredUrlIncidentRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method MonitoredUrlIncidentSelect select(array $whereEquals = [])
 * @method MonitoredUrlIncidentRecord newRecord(array $fields = [])
 * @method MonitoredUrlIncidentRecord[] newRecords(array $fieldSets)
 * @method MonitoredUrlIncidentRecordSet newRecordSet(array $records = [])
 * @method MonitoredUrlIncidentRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method MonitoredUrlIncidentRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class MonitoredUrlIncident extends Mapper
{
}
