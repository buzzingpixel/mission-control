<?php
declare(strict_types=1);

namespace src\app\data\PipelineItemServer;

use Atlas\Mapper\RecordSet;

/**
 * @method PipelineItemServerRecord offsetGet($offset)
 * @method PipelineItemServerRecord appendNew(array $fields = [])
 * @method PipelineItemServerRecord|null getOneBy(array $whereEquals)
 * @method PipelineItemServerRecordSet getAllBy(array $whereEquals)
 * @method PipelineItemServerRecord|null detachOneBy(array $whereEquals)
 * @method PipelineItemServerRecordSet detachAllBy(array $whereEquals)
 * @method PipelineItemServerRecordSet detachAll()
 * @method PipelineItemServerRecordSet detachDeleted()
 */
class PipelineItemServerRecordSet extends RecordSet
{
}
