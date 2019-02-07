<?php
declare(strict_types=1);

namespace src\app\data\Server;

use Atlas\Mapper\MapperSelect;

/**
 * @method ServerRecord|null fetchRecord()
 * @method ServerRecord[] fetchRecords()
 * @method ServerRecordSet fetchRecordSet()
 */
class ServerSelect extends MapperSelect
{
}
