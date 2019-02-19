<?php
declare(strict_types=1);

namespace src\app\projects;

use corbomite\di\Di;
use src\app\projects\models\ProjectModel;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\projects\services\SaveProjectService;
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
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(): ProjectModelInterface
    {
        return new ProjectModel();
    }

    /**
     * @throws InvalidProjectModelException
     * @throws ProjectNameNotUniqueException
     */
    public function save(ProjectModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveProjectService::class);
        $service->save($model);
    }

    public function archive(ProjectModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(ArchiveProjectService::class);
        $service->archive($model);
    }

    public function unArchive(ProjectModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(UnArchiveProjectService::class);
        $service->unArchive($model);
    }

    public function delete(ProjectModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DeleteProjectService::class);
        $service->delete($model);
    }

    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ProjectModelInterface {
        $this->limit = 1;
        $result = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;
        return $result;
    }

    /**
     * @return ProjectModelInterface[]
     */
    public function fetchAll(
        ?QueryModelInterface $params = null
    ): array {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchProjectsService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        if ($this->limit) {
            $params->limit($this->limit);
        }

        return $service->fetch($params);
    }

    public function fetchAsSelectArray(
        ?QueryModelInterface $params = null,
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
