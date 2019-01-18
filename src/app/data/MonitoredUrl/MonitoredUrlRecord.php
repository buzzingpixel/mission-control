<?php
declare(strict_types=1);

namespace src\app\data\MonitoredUrl;

use Atlas\Mapper\Record;

/**
 * @method MonitoredUrlRow getRow()
 */
class MonitoredUrlRecord extends Record
{
    use MonitoredUrlFields;
}
