<?php
declare(strict_types=1);

namespace src\app\data\Migration;

use Atlas\Mapper\MapperSelect;

/**
 * @method MigrationRecord|null fetchRecord()
 * @method MigrationRecord[] fetchRecords()
 * @method MigrationRecordSet fetchRecordSet()
 */
class MigrationSelect extends MapperSelect
{
}
