<?php
declare(strict_types=1);

namespace src\app\data\Migration;

use Atlas\Mapper\Record;

/**
 * @method MigrationRow getRow()
 */
class MigrationRecord extends Record
{
    use MigrationFields;
}
