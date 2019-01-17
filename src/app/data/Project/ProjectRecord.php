<?php
declare(strict_types=1);

namespace src\app\data\Project;

use Atlas\Mapper\Record;

/**
 * @method ProjectRow getRow()
 */
class ProjectRecord extends Record
{
    use ProjectFields;
}
