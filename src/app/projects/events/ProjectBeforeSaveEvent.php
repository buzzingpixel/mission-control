<?php
declare(strict_types=1);

namespace src\app\projects\events;

use src\app\projects\ProjectsApi;
use corbomite\events\interfaces\EventInterface;
use src\app\projects\interfaces\ProjectModelInterface;

class ProjectBeforeSaveEvent implements EventInterface
{
    private $isNew;
    private $projectModel;

    public function __construct(
        ProjectModelInterface $projectModel,
        bool $isNew = false
    ) {
        $this->isNew = $isNew;
        $this->projectModel = $projectModel;
    }

    public function isNew(): bool
    {
        return $this->isNew;
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
        return 'ProjectBeforeSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop !== null ? $stop : $this->stop;
    }
}
