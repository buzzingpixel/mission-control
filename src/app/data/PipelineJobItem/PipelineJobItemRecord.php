<?php
declare(strict_types=1);

namespace src\app\data\PipelineJobItem;

use Atlas\Mapper\Record;

/**
 * @method PipelineJobItemRow getRow()
 */
class PipelineJobItemRecord extends Record
{
    use PipelineJobItemFields;
}
