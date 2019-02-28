<?php
declare(strict_types=1);

namespace src\app\data\Pipeline;

use Atlas\Mapper\MapperSelect;

/**
 * @method PipelineRecord|null fetchRecord()
 * @method PipelineRecord[] fetchRecords()
 * @method PipelineRecordSet fetchRecordSet()
 */
class PipelineSelect extends MapperSelect
{
}
