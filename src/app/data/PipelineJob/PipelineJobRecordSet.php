<?php
declare(strict_types=1);

namespace src\app\data\PipelineJob;

use Atlas\Mapper\RecordSet;

/**
 * @method PipelineJobRecord offsetGet($offset)
 * @method PipelineJobRecord appendNew(array $fields = [])
 * @method PipelineJobRecord|null getOneBy(array $whereEquals)
 * @method PipelineJobRecordSet getAllBy(array $whereEquals)
 * @method PipelineJobRecord|null detachOneBy(array $whereEquals)
 * @method PipelineJobRecordSet detachAllBy(array $whereEquals)
 * @method PipelineJobRecordSet detachAll()
 * @method PipelineJobRecordSet detachDeleted()
 */
class PipelineJobRecordSet extends RecordSet
{
}
