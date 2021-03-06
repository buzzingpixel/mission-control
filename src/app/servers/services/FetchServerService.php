<?php

declare(strict_types=1);

namespace src\app\servers\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\data\Server\Server;
use src\app\data\Server\ServerRecord;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\transformers\ServerRecordModelTransformer;

class FetchServerService
{
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var ServerRecordModelTransformer */
    private $serverRecordModelTransformer;

    public function __construct(
        BuildQueryInterface $buildQuery,
        ServerRecordModelTransformer $serverRecordModelTransformer
    ) {
        $this->buildQuery                   = $buildQuery;
        $this->serverRecordModelTransformer = $serverRecordModelTransformer;
    }

    /**
     * @return ServerModelInterface[]
     */
    public function __invoke(QueryModelInterface $params) : array
    {
        return $this->fetch($params);
    }

    /**
     * @return ServerModelInterface[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        return $this->serverRecordModelTransformer->transformRecordSet(
            $this->fetchResults($params)
        );
    }

    /**
     * @return ServerRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(Server::class, $params)->fetchRecords();
    }
}
