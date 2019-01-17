<?php
declare(strict_types=1);

namespace src\app\projects\interfaces;

use src\app\projects\exceptions\ProjectNameNotUniqueException;
use src\app\projects\exceptions\InvalidProjectModelException;

interface ProjectsApiInterface
{
    /**
     * Creates a Project Model
     * @param array $props
     * @return ProjectModelInterface
     */
    public function createProjectModel(array $props = []): ProjectModelInterface;

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
}
