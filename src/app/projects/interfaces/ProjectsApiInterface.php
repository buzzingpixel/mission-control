<?php
declare(strict_types=1);

namespace src\app\projects\interfaces;

interface ProjectsApiInterface
{
    /**
     * Creates a Project Model
     * @param array $props
     * @return ProjectModelInterface
     */
    public function createProjectModel(array $props = []): ProjectModelInterface;

    /**
     * Saves a project (creating if necesary)
     * @param ProjectModelInterface $projectModel
     * @return mixed
     */
    public function saveProject(ProjectModelInterface $projectModel);

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
