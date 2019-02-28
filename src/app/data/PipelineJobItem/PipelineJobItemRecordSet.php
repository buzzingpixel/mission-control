<?php
declare(strict_types=1);

namespace src\app\data\PipelineJobItem;

use Atlas\Mapper\RecordSet;

/**
 * @method PipelineJobItemRecord offsetGet($offset)
 * @method PipelineJobItemRecord appendNew(array $fields = [])
 * @method PipelineJobItemRecord|null getOneBy(array $whereEquals)
 * @method PipelineJobItemRecordSet getAllBy(array $whereEquals)
 * @method PipelineJobItemRecord|null detachOneBy(array $whereEquals)
 * @method PipelineJobItemRecordSet detachAllBy(array $whereEquals)
 * @method PipelineJobItemRecordSet detachAll()
 * @method PipelineJobItemRecordSet detachDeleted()
 */
class PipelineJobItemRecordSet extends RecordSet
{
}
