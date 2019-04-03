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
     */
    public function createModel() : PingModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     */
    public function uuidToBytes(string $string) : string;

    /**
     * Creates a Fetch Data Params instance
     */
    public function makeQueryModel() : QueryModelInterface;

    /**
     * Saves a Ping Model
     *
     * @throws InvalidPingModelException
     * @throws PingNameNotUniqueException
     */
    public function save(PingModelInterface $model) : void;

    /**
     * Archives a ping
     */
    public function archive(PingModelInterface $model) : void;

    /**
     * Un-archives a ping
     */
    public function unArchive(PingModelInterface $model) : void;

    /**
     * Deletes a ping
     *
     * @return mixed
     */
    public function delete(PingModelInterface $model);

    /**
     * Fetches one ping model result based on params
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?PingModelInterface;

    /**
     * Fetches all ping models based on params
     *
     * @return PingModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array;
}
