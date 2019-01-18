<?php
declare(strict_types=1);

namespace src\app\data\MonitoredUrl;

use Atlas\Mapper\RecordSet;

/**
 * @method MonitoredUrlRecord offsetGet($offset)
 * @method MonitoredUrlRecord appendNew(array $fields = [])
 * @method MonitoredUrlRecord|null getOneBy(array $whereEquals)
 * @method MonitoredUrlRecordSet getAllBy(array $whereEquals)
 * @method MonitoredUrlRecord|null detachOneBy(array $whereEquals)
 * @method MonitoredUrlRecordSet detachAllBy(array $whereEquals)
 * @method MonitoredUrlRecordSet detachAll()
 * @method MonitoredUrlRecordSet detachDeleted()
 */
class MonitoredUrlRecordSet extends RecordSet
{
}
