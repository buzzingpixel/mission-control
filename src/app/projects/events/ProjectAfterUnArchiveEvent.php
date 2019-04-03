<?php

declare(strict_types=1);

namespace src\app\projects\events;

use corbomite\events\interfaces\EventInterface;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\ProjectsApi;

class ProjectAfterUnArchiveEvent implements EventInterface
{
    /** @var ProjectModelInterface */
    private $projectModel;

    public function __construct(ProjectModelInterface $projectModel)
    {
        $this->projectModel = $projectModel;
    }

    public function projectModel() : ProjectModelInterface
    {
        return $this->projectModel;
    }

    public function provider() : string
    {
        return ProjectsApi::class;
    }

    public function name() : string
    {
        return 'ProjectAfterUnArchive';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
