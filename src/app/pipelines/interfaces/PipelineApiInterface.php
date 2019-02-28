<?php
declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\pipelines\exceptions\InvalidPipelineModel;
use src\app\servers\exceptions\TitleNotUniqueException;

interface PipelineApiInterface
{
    /**
     * Creates a Pipeline Model
     * @return PipelineModelInterface
     */
    public function createPipelineModel(): PipelineModelInterface;

    /**
     * Creates a Pipeline Item Model
     * @return PipelineItemModelInterface
     */
    public function createPipelineItemModel(): PipelineItemModelInterface;

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
     * Saves a Pipeline
     * @param PipelineModelInterface $model
     * @return mixed
     * @throws InvalidPipelineModel
     * @throws TitleNotUniqueException
     */
    public function save(PipelineModelInterface $model);

    /**
     * Archives a Pipeline
     * @param PipelineModelInterface $model
     */
    public function archive(PipelineModelInterface $model);

    /**
     * Un-archives a Pipeline
     * @param PipelineModelInterface $model
     */
    public function unArchive(PipelineModelInterface $model);

    /**
     * Deletes a Pipeline
     * @param PipelineModelInterface $model
     * @return mixed
     */
    public function delete(PipelineModelInterface $model);

    /**
     * Fetches one Pipeline model result based on params
     * @param QueryModelInterface $params
     * @return PipelineModelInterface|null
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?PipelineModelInterface;

    /**
     * Fetches all Pipeline models based on params
     * @param QueryModelInterface $params
     * @return PipelineModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null): array;
}
