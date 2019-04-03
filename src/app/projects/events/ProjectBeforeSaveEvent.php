<?php

declare(strict_types=1);

namespace src\app\projects\events;

use corbomite\events\interfaces\EventInterface;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\ProjectsApi;

class ProjectBeforeSaveEvent implements EventInterface
{
    /** @var bool */
    private $isNew;
    /** @var ProjectModelInterface */
    private $projectModel;

    public function __construct(
        ProjectModelInterface $projectModel,
        bool $isNew = false
    ) {
        $this->isNew        = $isNew;
        $this->projectModel = $projectModel;
    }

    public function isNew() : bool
    {
        return $this->isNew;
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
        return 'ProjectBeforeSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
