<?php
declare(strict_types=1);

namespace src\app\projects;

use corbomite\di\Di;
use src\app\projects\models\ProjectModel;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\projects\services\SaveProjectService;
use src\app\datasupport\FetchDataParamsInterface;
use src\app\projects\services\FetchProjectsService;
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

    public function createFetchDataParams(): FetchDataParamsInterface
    {
        return (new FetchDataParamsFactory())->make();
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

    public function fetchProject(FetchDataParamsInterface $params): ?ProjectModelInterface
    {
        return $this->fetchProjects($params)[0] ?? null;
    }

    /**
     * @return ProjectModelInterface[]
     */
    public function fetchProjects(FetchDataParamsInterface $params): array
    {
        return $this->di->getFromDefinition(FetchProjectsService::class)->fetch(
            $params
        );
    }
}
