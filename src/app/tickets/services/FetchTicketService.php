<?php

declare(strict_types=1);

namespace src\app\tickets\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeImmutable;
use DateTimeZone;
use src\app\data\Ticket\Ticket;
use src\app\data\Ticket\TicketRecord;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\models\TicketModel;
use function array_values;

class FetchTicketService
{
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var UserApiInterface */
    private $userApi;

    public function __construct(
        BuildQueryInterface $buildQuery,
        UserApiInterface $userApi
    ) {
        $this->buildQuery = $buildQuery;
        $this->userApi    = $userApi;
    }

    /**
     * @return TicketModelContract[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $results = $this->fetchResults($params);

        $models = [];

        $userIds = [];

        foreach ($results as $record) {
            $userIds[$record->created_by_user_guid] = $record->created_by_user_guid;

            $userIds[$record->assigned_to_user_guid] = $record->assigned_to_user_guid;
        }

        $userQueryParams = $this->userApi->makeQueryModel();

        $userQueryParams->addWhere('guid', array_values($userIds));

        $users = [];

        foreach ($this->userApi->fetchAll($userQueryParams) as $userModel) {
            $users[$userModel->getGuidAsBytes()] = $userModel;
        }

        foreach ($results as $record) {
            $model = new TicketModel();

            $model->setGuidAsBytes($record->guid);

            $model->createdByUser($users[$record->created_by_user_guid] ?? null);

            $model->assignedToUser($users[$record->assigned_to_user_guid] ?? null);

            $model->title($record->title);

            $model->content($record->content);

            $model->status($record->status);

            if ($record->added_at_utc) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->addedAt(new DateTimeImmutable($record->added_at_utc, new DateTimeZone('UTC')));
            }

            if ($record->resolved_at_utc) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->addedAt(new DateTimeImmutable($record->resolved_at_utc, new DateTimeZone('UTC')));
            }

            // TODO: Thread Items

            // TODO: Watchers

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return TicketRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(Ticket::class, $params)->fetchRecords();
    }
}
