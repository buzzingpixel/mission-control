<?php
declare(strict_types=1);

namespace src\app\data\Ping;

use Atlas\Mapper\Record;

/**
 * @method PingRow getRow()
 */
class PingRecord extends Record
{
    use PingFields;
}
