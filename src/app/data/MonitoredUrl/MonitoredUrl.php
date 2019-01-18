<?php
declare(strict_types=1);

namespace src\app\data\MonitoredUrl;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method MonitoredUrlTable getTable()
 * @method MonitoredUrlRelationships getRelationships()
 * @method MonitoredUrlRecord|null fetchRecord($primaryVal, array $with = [])
 * @method MonitoredUrlRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method MonitoredUrlRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method MonitoredUrlRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method MonitoredUrlRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method MonitoredUrlRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method MonitoredUrlSelect select(array $whereEquals = [])
 * @method MonitoredUrlRecord newRecord(array $fields = [])
 * @method MonitoredUrlRecord[] newRecords(array $fieldSets)
 * @method MonitoredUrlRecordSet newRecordSet(array $records = [])
 * @method MonitoredUrlRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method MonitoredUrlRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class MonitoredUrl extends Mapper
{
}
