<?php
declare(strict_types=1);

namespace src\app\projects;

use corbomite\di\Di;
use corbomite\db\models\UuidModel;
use corbomite\db\Factory as DbFactory;
use src\app\projects\models\ProjectModel;
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
    private $di;
    private $dbFactory;

    public function __construct(Di $di, DbFactory $dbFactory)
    {
        $this->di = $di;
        $this->dbFactory = $dbFactory;
    }

    public function createModel(array $props = []): ProjectModelInterface
    {
        return new ProjectModel($props);
    }

    public function uuidToBytes(string $string): string
    {
        return (new UuidModel($string))->toBytes();
    }

    public function makeQueryModel(): QueryModelInterface
    {
        return $this->dbFactory->makeQueryModel();
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

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ProjectModelInterface {
        return $this->fetchAll($params)[0] ?? null;
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
