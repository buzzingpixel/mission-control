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
use src\app\projects\services\UnArchiveProjectService;
use src\app\projects\exceptions\InvalidProjectModelException;
use src\app\projects\exceptions\ProjectNameNotUniqueException;

class ProjectsApi implements ProjectsApiInterface
{
    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(array $props = []): ProjectModelInterface
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
    public function save(ProjectModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveProjectService::class);
        $service->save($model);
    }

    public function archive(ProjectModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(ArchiveProjectService::class);
        $service->archive($model);
    }

    public function unArchive(ProjectModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(UnArchiveProjectService::class);
        $service->unArchive($model);
    }

    public function delete(ProjectModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DeleteProjectService::class);
        $service->delete($model);
    }

    public function fetchOne(
        ?FetchDataParamsInterface $params = null
    ): ?ProjectModelInterface {
        return $this->fetchAll($params)[0] ?? null;
    }

    /**
     * @return ProjectModelInterface[]
     */
    public function fetchAll(
        ?FetchDataParamsInterface $params = null
    ): array {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchProjectsService::class);

        if (! $params) {
            $params = $this->createFetchDataParams();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        return $service->fetch($params ?? $this->createFetchDataParams());
    }

    public function fetchAsSelectArray(
        ?FetchDataParamsInterface $params = null,
        $keyIsSlug = false
    ): array {
        $projects = $this->fetchAll($params);

        $items = [];

        foreach ($projects as $project) {
            $key = $keyIsSlug ? $project->slug() : $project->guid();
            $items[$key] = $project->title();
        }

        return $items;
    }
}
