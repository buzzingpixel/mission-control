<?php

declare(strict_types=1);

namespace src\app\tickets\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\data\Ticket\Ticket;

class CountAllTickets
{
    /** @var BuildQueryInterface */
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    public function count(?QueryModelInterface $params = null) : int
    {
        return $this->buildQuery->build(Ticket::class, $params)->fetchCount();
    }
}
