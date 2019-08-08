<?php

declare(strict_types=1);

namespace src\app\tickets\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\data\TicketThreadItem\TicketThreadItem;
use src\app\data\TicketThreadItem\TicketThreadItemRecord;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use function array_values;

class FetchThreadItemsService
{
    use MakeQueryModelTrait;

    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var FetchTicketService */
    private $fetchTicket;

    public function __construct(
        BuildQueryInterface $buildQuery,
        FetchTicketService $fetchTicket
    ) {
        $this->buildQuery  = $buildQuery;
        $this->fetchTicket = $fetchTicket;
    }

    /**
     * @return TicketThreadItemModelContract[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $results = $this->fetchResults($params);

        if (! $results) {
            return [];
        }

        $resultsById = $ticketIds = [];

        foreach ($results as $record) {
            $resultsById[$record->guid] = $record;
            $ticketIds[]                = $record->ticket_guid;
        }

        if (! $ticketIds) {
            return [];
        }

        $params = $this->makeQueryModel();
        $params->addWhere('guid', $ticketIds);
        $tickets = $this->fetchTicket->fetch($params);

        foreach ($tickets as $ticket) {
            foreach ($ticket->threadItems() as $threadItem) {
                $resultsById[$threadItem->getGuidAsBytes()] = $threadItem;
            }
        }

        return array_values($resultsById);
    }

    /**
     * @return TicketThreadItemRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(TicketThreadItem::class, $params)->fetchRecords();
    }
}
