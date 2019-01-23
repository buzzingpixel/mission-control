<?php
declare(strict_types=1);

namespace src\app\projects\models;

use corbomite\db\traits\UuidTrait;
use src\app\support\traits\ModelAddedAtTrait;
use src\app\projects\interfaces\ProjectModelInterface;
use \src\app\support\traits\StandardModelStandaloneTrait;

class ProjectModel implements ProjectModelInterface
{
    use UuidTrait;
    use ModelAddedAtTrait;
    use StandardModelStandaloneTrait;

    private $description = '';

    public function description(?string $description = null): string
    {
        return $this->description = $description ?? $this->description;
    }
}
