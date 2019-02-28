<?php
declare(strict_types=1);

namespace src\app\data\Pipeline;

use Atlas\Mapper\Record;

/**
 * @method PipelineRow getRow()
 */
class PipelineRecord extends Record
{
    use PipelineFields;
}
