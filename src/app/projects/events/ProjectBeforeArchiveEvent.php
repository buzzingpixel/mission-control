<?php
declare(strict_types=1);

namespace src\app\projects\events;

use src\app\projects\ProjectsApi;
use corbomite\events\interfaces\EventInterface;
use src\app\projects\interfaces\ProjectModelInterface;

class ProjectBeforeArchiveEvent implements EventInterface
{
    private $projectModel;

    public function __construct(ProjectModelInterface $projectModel)
    {
        $this->projectModel = $projectModel;
    }

    public function projectModel(): ProjectModelInterface
    {
        return $this->projectModel;
    }

    public function provider(): string
    {
        return ProjectsApi::class;
    }

    public function name(): string
    {
        return 'ProjectBeforeArchive';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
