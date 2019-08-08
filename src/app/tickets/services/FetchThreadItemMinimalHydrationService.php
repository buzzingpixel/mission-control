<?php

declare(strict_types=1);

namespace src\app\tickets\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use DateTimeImmutable;
use DateTimeZone;
use src\app\data\TicketThreadItem\TicketThreadItem;
use src\app\data\TicketThreadItem\TicketThreadItemRecord;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use src\app\tickets\models\TicketThreadItemModel;

class FetchThreadItemMinimalHydrationService
{
    /** @var BuildQueryInterface */
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
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

        $models = [];

        foreach ($results as $record) {
            $model = new TicketThreadItemModel();

            $model->ticketGuid = $record->ticket_guid;

            $model->userGuid = $record->user_guid;

            $model->setGuidAsBytes($record->guid);

            $model->content($record->content);

            if ($record->added_at_utc) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->addedAt(new DateTimeImmutable($record->added_at_utc, new DateTimeZone('UTC')));
            }

            $model->hasBeenModified(
                $record->has_been_modified === 1 ||
                $record->has_been_modified === '1' ||
                $record->has_been_modified === true ||
                $record->has_been_modified === 'true'
            );

            if ($record->modified_at_utc) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->modifiedAt(new DateTimeImmutable($record->modified_at_utc, new DateTimeZone('UTC')));
            }

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return TicketThreadItemRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(TicketThreadItem::class, $params)->fetchRecords();
    }
}
