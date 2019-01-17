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
    public function createProjectModel(array $props = []): ProjectModelInterface;

    /**
     * Creates a Fetch Data Params instance
     * @return FetchDataParamsInterface
     */
    public function createFetchDataParams(): FetchDataParamsInterface;

    /**
     * Saves a project (creating if necessary)
     * @param ProjectModelInterface $projectModel
     * @return mixed
     * @throws InvalidProjectModelException
     * @throws ProjectNameNotUniqueException
     */
    public function saveProject(ProjectModelInterface $model);

    /**
     * Archives a project
     * @param ProjectModelInterface $projectModel
     * @return mixed
     */
    public function archiveProject(ProjectModelInterface $projectModel);

    /**
     * Deletes a project
     * @param ProjectModelInterface $projectModel
     * @return mixed
     */
    public function deleteProject(ProjectModelInterface $projectModel);

    /**
     * Fetches project based on params
     * @param FetchDataParamsInterface $params
     * @return ProjectModelInterface|null
     */
    public function fetchProject(FetchDataParamsInterface $params): ?ProjectModelInterface;

    /**
     * Fetches projects based on param
     * @return ProjectModelInterface[]
     */
    public function fetchProjects(FetchDataParamsInterface $params): array;
}
