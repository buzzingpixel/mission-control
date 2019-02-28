<?php
declare(strict_types=1);

namespace src\app\data\PipelineJob;

use Atlas\Mapper\Record;

/**
 * @method PipelineJobRow getRow()
 */
class PipelineJobRecord extends Record
{
    use PipelineJobFields;
}
