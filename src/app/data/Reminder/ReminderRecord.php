<?php
declare(strict_types=1);

namespace src\app\data\Reminder;

use Atlas\Mapper\Record;

/**
 * @method ReminderRow getRow()
 */
class ReminderRecord extends Record
{
    use ReminderFields;
}
