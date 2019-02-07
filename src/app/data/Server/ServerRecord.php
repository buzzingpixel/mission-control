<?php
declare(strict_types=1);

namespace src\app\data\Server;

use Atlas\Mapper\Record;

/**
 * @method ServerRow getRow()
 */
class ServerRecord extends Record
{
    use ServerFields;
}
