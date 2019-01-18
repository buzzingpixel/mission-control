<?php
declare(strict_types=1);

namespace src\app\data\MonitoredUrlIncident;

use Atlas\Mapper\Record;

/**
 * @method MonitoredUrlIncidentRow getRow()
 */
class MonitoredUrlIncidentRecord extends Record
{
    use MonitoredUrlIncidentFields;
}
