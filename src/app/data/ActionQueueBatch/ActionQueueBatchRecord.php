<?php
declare(strict_types=1);

namespace src\app\data\ActionQueueBatch;

use Atlas\Mapper\Record;

/**
 * @method ActionQueueBatchRow getRow()
 */
class ActionQueueBatchRecord extends Record
{
    use ActionQueueBatchFields;
}
