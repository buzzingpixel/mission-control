<?php
declare(strict_types=1);

namespace src\app\projects\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\projects\exceptions\InvalidProjectModelException;
use src\app\projects\exceptions\ProjectNameNotUniqueException;

interface ProjectsApiInterface
{
    /**
     * Creates a Project Model
     * @param array $props
     * @return ProjectModelInterface
     */
    public function createModel(): ProjectModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     * @param string $string
     * @return string
     */
    public function uuidToBytes(string $string): string;

    /**
     * Creates a Fetch Data Params instance
     * @return QueryModelInterface
     */
    public function makeQueryModel(): QueryModelInterface;

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
     * @param QueryModelInterface $params
     * @return ProjectModelInterface|null
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ProjectModelInterface;

    /**
     * Fetches projects based on param
     * @param QueryModelInterface|null $params
     * @return ProjectModelInterface[]
     */
    public function fetchAll(
        ?QueryModelInterface $params = null
    ): array;

    /**
     * Fetch projects as select array
     * @param QueryModelInterface|null $params
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     * @return array [
     *     'project-slug' => 'Project Name',
     *     'another-project-slug' => 'Another Project Name',
     * ]
     */
    public function fetchAsSelectArray(
        ?QueryModelInterface $params = null,
        $keyIsSlug = false
    ): array;
}
