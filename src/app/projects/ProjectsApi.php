<?php
declare(strict_types=1);

namespace src\app\projects;

use corbomite\di\Di;
use src\app\projects\models\ProjectModel;
use src\app\projects\services\SaveProjectService;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\exceptions\InvalidProjectModelException;

class ProjectsApi implements ProjectsApiInterface
{
    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createProjectModel(array $props = []): ProjectModelInterface
    {
        return new ProjectModel($props);
    }

    /**
     * @throws InvalidProjectModelException
     */
    public function saveProject(ProjectModelInterface $model)
    {
        $this->di->getFromDefinition(SaveProjectService::class)->save($model);
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
