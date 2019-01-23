<?php
declare(strict_types=1);

namespace src\app\pings\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\pings\exceptions\InvalidPingModelException;
use src\app\pings\exceptions\PingNameNotUniqueException;

interface PingApiInterface
{
    /**
     * Creates a Ping Model
     * @return PingModelInterface
     */
    public function createModel(): PingModelInterface;

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
     * Saves a Ping Model
     * @param PingModelInterface $model
     * @throws InvalidPingModelException
     * @throws PingNameNotUniqueException
     */
    public function save(PingModelInterface $model);

    /**
     * Archives a ping
     * @param PingModelInterface $model
     */
    public function archive(PingModelInterface $model);

    /**
     * Un-archives a ping
     * @param PingModelInterface $model
     */
    public function unArchive(PingModelInterface $model);

    /**
     * Deletes a ping
     * @param PingModelInterface $model
     * @return mixed
     */
    public function delete(PingModelInterface $model);

    /**
     * Fetches one ping model result based on params
     * @param QueryModelInterface $params
     * @return PingModelInterface|null
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?PingModelInterface;

    /**
     * Fetches all ping models based on params
     * @param QueryModelInterface $params
     * @return PingModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null): array;
}
