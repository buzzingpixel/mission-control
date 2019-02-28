<?php
declare(strict_types=1);

namespace src\app\data\Pipeline;

use Atlas\Mapper\RecordSet;

/**
 * @method PipelineRecord offsetGet($offset)
 * @method PipelineRecord appendNew(array $fields = [])
 * @method PipelineRecord|null getOneBy(array $whereEquals)
 * @method PipelineRecordSet getAllBy(array $whereEquals)
 * @method PipelineRecord|null detachOneBy(array $whereEquals)
 * @method PipelineRecordSet detachAllBy(array $whereEquals)
 * @method PipelineRecordSet detachAll()
 * @method PipelineRecordSet detachDeleted()
 */
class PipelineRecordSet extends RecordSet
{
}
