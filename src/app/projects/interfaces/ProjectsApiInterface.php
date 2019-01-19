<?php
declare(strict_types=1);

namespace src\app\projects\interfaces;

use src\app\datasupport\FetchDataParamsInterface;
use src\app\projects\exceptions\InvalidProjectModelException;
use src\app\projects\exceptions\ProjectNameNotUniqueException;

interface ProjectsApiInterface
{
    /**
     * Creates a Project Model
     * @param array $props
     * @return ProjectModelInterface
     */
    public function createModel(array $props = []): ProjectModelInterface;

    /**
     * Creates a Fetch Data Params instance
     * @return FetchDataParamsInterface
     */
    public function createFetchDataParams(): FetchDataParamsInterface;

    /**
     * Saves a project (creating if necessary)
     * @param ProjectModelInterface $projectModel
     * @throws InvalidProjectModelException
     * @throws ProjectNameNotUniqueException
     */
    public function save(ProjectModelInterface $model);

    /**
     * Archives a project
     * @param ProjectModelInterface $projectModel
     * @return mixed
     */
    public function archive(ProjectModelInterface $model);

    /**
     * Un-archives a project
     * @param ProjectModelInterface $projectModel
     * @return mixed
     */
    public function unArchive(ProjectModelInterface $model);

    /**
     * Deletes a project
     * @param ProjectModelInterface $projectModel
     * @return mixed
     */
    public function delete(ProjectModelInterface $model);

    /**
     * Fetches project based on params
     * @param FetchDataParamsInterface $params
     * @return ProjectModelInterface|null
     */
    public function fetchOne(
        ?FetchDataParamsInterface $params = null
    ): ?ProjectModelInterface;

    /**
     * Fetches projects based on param
     * @param FetchDataParamsInterface|null $params
     * @return ProjectModelInterface[]
     */
    public function fetchAll(
        ?FetchDataParamsInterface $params = null
    ): array;

    /**
     * Fetch projects as select array
     * @param FetchDataParamsInterface|null $params
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     * @return array [
     *     'project-slug' => 'Project Name',
     *     'another-project-slug' => 'Another Project Name',
     * ]
     */
    public function fetchAsSelectArray(
        ?FetchDataParamsInterface $params = null,
        $keyIsSlug = false
    ): array;
}
