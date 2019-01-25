<?php
declare(strict_types=1);

namespace src\app\data\NotificationEmail;

use Atlas\Mapper\Record;

/**
 * @method NotificationEmailRow getRow()
 */
class NotificationEmailRecord extends Record
{
    use NotificationEmailFields;
}
