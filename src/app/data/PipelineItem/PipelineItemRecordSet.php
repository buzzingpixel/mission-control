<?php
declare(strict_types=1);

namespace src\app\data\PipelineItem;

use Atlas\Mapper\RecordSet;

/**
 * @method PipelineItemRecord offsetGet($offset)
 * @method PipelineItemRecord appendNew(array $fields = [])
 * @method PipelineItemRecord|null getOneBy(array $whereEquals)
 * @method PipelineItemRecordSet getAllBy(array $whereEquals)
 * @method PipelineItemRecord|null detachOneBy(array $whereEquals)
 * @method PipelineItemRecordSet detachAllBy(array $whereEquals)
 * @method PipelineItemRecordSet detachAll()
 * @method PipelineItemRecordSet detachDeleted()
 */
class PipelineItemRecordSet extends RecordSet
{
}
