<?php
declare(strict_types=1);

namespace src\app\projects;

use corbomite\di\Di;
use src\app\projects\models\ProjectModel;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\projects\services\SaveProjectService;
use src\app\datasupport\FetchDataParamsInterface;
use src\app\projects\services\DeleteProjectService;
use src\app\projects\services\FetchProjectsService;
use src\app\projects\services\ArchiveProjectService;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\exceptions\InvalidProjectModelException;
use src\app\projects\exceptions\ProjectNameNotUniqueException;

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
     * @throws ProjectNameNotUniqueException
     */
    public function saveProject(ProjectModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveProjectService::class);
        $service->save($model);
    }

    public function archiveProject(ProjectModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(ArchiveProjectService::class);
        $service->archive($model);
    }

    public function deleteProject(ProjectModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DeleteProjectService::class);
        $service->delete($model);
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
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchProjectsService::class);
        return $service->fetch($params);
    }
}
