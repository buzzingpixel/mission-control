<?php
declare(strict_types=1);

namespace src\app\data\UserPasswordResetToken;

use Atlas\Mapper\Record;

/**
 * @method UserPasswordResetTokenRow getRow()
 */
class UserPasswordResetTokenRecord extends Record
{
    use UserPasswordResetTokenFields;
}
