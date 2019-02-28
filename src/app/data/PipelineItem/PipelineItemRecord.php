<?php
declare(strict_types=1);

namespace src\app\data\PipelineItem;

use Atlas\Mapper\Record;

/**
 * @method PipelineItemRow getRow()
 */
class PipelineItemRecord extends Record
{
    use PipelineItemFields;
}
