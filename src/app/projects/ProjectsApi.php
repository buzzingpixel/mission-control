<?php
declare(strict_types=1);

namespace src\app\projects;

use src\app\projects\models\ProjectModel;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\projects\interfaces\ProjectModelInterface;

class ProjectsApi implements ProjectsApiInterface
{
    public function createProjectModel(array $props = []): ProjectModelInterface
    {
        return new ProjectModel($props);
    }

    public function saveProject(ProjectModelInterface $projectModel)
    {
        // TODO: Implement method call to a service
    }

    public function archiveProject(ProjectModelInterface $projectModel)
    {
        // TODO: Implement method call to a service
    }

    public function deleteProject(ProjectModelInterface $projectModel)
    {
        // TODO: Implement method call to a service
    }
}
