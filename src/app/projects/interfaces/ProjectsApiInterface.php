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
     */
    public function createModel() : ProjectModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     */
    public function uuidToBytes(string $string) : string;

    /**
     * Creates a Fetch Data Params instance
     */
    public function makeQueryModel() : QueryModelInterface;

    /**
     * Saves a project (creating if necessary)
     *
     * @param ProjectModelInterface $projectModel
     *
     * @return mixed
     *
     * @throws InvalidProjectModelException
     * @throws ProjectNameNotUniqueException
     */
    public function save(ProjectModelInterface $model);

    /**
     * Archives a project
     *
     * @param ProjectModelInterface $projectModel
     *
     * @return mixed
     */
    public function archive(ProjectModelInterface $model);

    /**
     * Un-archives a project
     *
     * @param ProjectModelInterface $projectModel
     *
     * @return mixed
     */
    public function unArchive(ProjectModelInterface $model);

    /**
     * Deletes a project
     *
     * @param ProjectModelInterface $projectModel
     *
     * @return mixed
     */
    public function delete(ProjectModelInterface $model);

    /**
     * Fetches project based on params
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?ProjectModelInterface;

    /**
     * Fetches projects based on param
     *
     * @return ProjectModelInterface[]
     */
    public function fetchAll(
        ?QueryModelInterface $params = null
    ) : array;

    /**
     * Fetch projects as select array
     *
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     *
     * @return string[] [
     *     'project-slug' => 'Project Name',
     *     'another-project-slug' => 'Another Project Name',
     * ]
     */
    public function fetchAsSelectArray(
        ?QueryModelInterface $params = null,
        bool $keyIsSlug = false
    ) : array;
}
