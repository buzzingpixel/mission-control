<?php
declare(strict_types=1);

namespace src\app\projects\events;

use src\app\projects\ProjectsApi;
use corbomite\events\interfaces\EventInterface;
use src\app\projects\interfaces\ProjectModelInterface;

class ProjectAfterSaveEvent implements EventInterface
{
    private $wasNew;
    private $projectModel;

    public function __construct(
        ProjectModelInterface $projectModel,
        bool $wasNew = false
    ) {
        $this->wasNew = $wasNew;
        $this->projectModel = $projectModel;
    }

    public function wasNew(): bool
    {
        return $this->wasNew;
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
        return 'ProjectAfterSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
