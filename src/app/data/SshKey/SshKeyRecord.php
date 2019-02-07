<?php
declare(strict_types=1);

namespace src\app\data\SshKey;

use Atlas\Mapper\Record;

/**
 * @method SshKeyRow getRow()
 */
class SshKeyRecord extends Record
{
    use SshKeyFields;
}
