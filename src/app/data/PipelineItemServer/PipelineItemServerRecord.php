<?php
declare(strict_types=1);

namespace src\app\data\PipelineItemServer;

use Atlas\Mapper\Record;

/**
 * @method PipelineItemServerRow getRow()
 */
class PipelineItemServerRecord extends Record
{
    use PipelineItemServerFields;
}
