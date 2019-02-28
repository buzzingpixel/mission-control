<?php
declare(strict_types=1);

namespace src\app\data\Pipeline;

use Atlas\Table\TableSelect;

/**
 * @method PipelineRow|null fetchRow()
 * @method PipelineRow[] fetchRows()
 */
class PipelineTableSelect extends TableSelect
{
}
