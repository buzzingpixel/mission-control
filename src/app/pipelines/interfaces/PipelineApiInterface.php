<?php

declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\pipelines\exceptions\InvalidPipelineJobModel;
use src\app\pipelines\exceptions\InvalidPipelineModel;
use src\app\servers\exceptions\TitleNotUniqueException;

interface PipelineApiInterface
{
    /**
     * Creates a Pipeline Model
     */
    public function createPipelineModel() : PipelineModelInterface;

    /**
     * Creates a Pipeline Item Model
     */
    public function createPipelineItemModel() : PipelineItemModelInterface;

    /**
     * Creates a Pipeline Job Model
     */
    public function createPipelineJobModel() : PipelineJobModelInterface;

    /**
     * Creates a Pipeline Job Item Model
     */
    public function createPipelineJobItemModel() : PipelineJobItemModelInterface;

    /**
     * Creates a Pipeline Job from a Pipeline Model
     *
     * @return mixed
     */
    public function initJobFromPipelineModel(PipelineModelInterface $pipelineModel);

    /**
     * Converts a UUID to bytes for database queries
     */
    public function uuidToBytes(string $string) : string;

    /**
     * Creates a Fetch Data Params instance
     */
    public function makeQueryModel() : QueryModelInterface;

    /**
     * Saves a Pipeline
     *
     * @return mixed
     *
     * @throws InvalidPipelineModel
     * @throws TitleNotUniqueException
     */
    public function save(PipelineModelInterface $model);

    /**
     * Saves a Pipeline Job
     *
     * @return mixed
     *
     * @throws InvalidPipelineJobModel
     */
    public function saveJob(PipelineJobModelInterface $model);

    /**
     * Archives a Pipeline
     *
     * @return mixed
     */
    public function archive(PipelineModelInterface $model);

    /**
     * Un-archives a Pipeline
     *
     * @return mixed
     */
    public function unArchive(PipelineModelInterface $model);

    /**
     * Deletes a Pipeline
     *
     * @return mixed
     */
    public function delete(PipelineModelInterface $model);

    /**
     * Fetches one Pipeline model result based on params
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?PipelineModelInterface;

    /**
     * Fetches all Pipeline models based on params
     *
     * @return PipelineModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array;

    /**
     * Fetches one Pipeline job model result based on params
     */
    public function fetchOneJob(
        ?QueryModelInterface $params = null
    ) : ?PipelineJobModelInterface;

    /**
     * Fetches all Pipeline job models based on params
     *
     * @return PipelineJobModelInterface[]
     */
    public function fetchAllJobs(?QueryModelInterface $params = null) : array;

    /**
     * Fetches one Pipeline Job Item model based on params
     */
    public function fetchOneJobItem(?QueryModelInterface $params = null) : ?PipelineJobItemModelInterface;
}
