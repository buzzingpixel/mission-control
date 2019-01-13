<?php
declare(strict_types=1);

namespace src\app\data\ActionQueueItem;

use Atlas\Mapper\Record;

/**
 * @method ActionQueueItemRow getRow()
 */
class ActionQueueItemRecord extends Record
{
    use ActionQueueItemFields;
}
