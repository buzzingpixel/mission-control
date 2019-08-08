<?php

declare(strict_types=1);

namespace src\app\tickets\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\models\UuidModel;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeImmutable;
use DateTimeZone;
use src\app\data\Ticket\Ticket;
use src\app\data\Ticket\TicketRecord;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\models\TicketModel;
use function array_values;
use function json_decode;

class FetchTicketService
{
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var UserApiInterface */
    private $userApi;
    /** @var FetchThreadItemMinimalHydrationService */
    private $fetchThreadItem;

    public function __construct(
        BuildQueryInterface $buildQuery,
        UserApiInterface $userApi,
        FetchThreadItemMinimalHydrationService $fetchThreadItem
    ) {
        $this->buildQuery      = $buildQuery;
        $this->userApi         = $userApi;
        $this->fetchThreadItem = $fetchThreadItem;
    }

    /**
     * @return TicketModelContract[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $results = $this->fetchResults($params);

        if (! $results) {
            return [];
        }

        $models = $ticketIds = $userIds = $users = $threadItems = [];

        foreach ($results as $record) {
            $ticketIds[$record->guid] = $record->guid;
        }

        if ($ticketIds) {
            $queryParams = $this->userApi->makeQueryModel();

            $queryParams->addWhere('ticket_guid', $ticketIds);

            $queryParams->addOrder('added_at_utc', 'asc');

            foreach ($this->fetchThreadItem->fetch($queryParams) as $threadItemModel) {
                $threadItems[$threadItemModel->ticketGuid][] = $threadItemModel;
                $userIds[$threadItemModel->userGuid]         = $threadItemModel->userGuid;
            }
        }

        foreach ($results as $record) {
            $userIds[$record->created_by_user_guid] = $record->created_by_user_guid;

            $userIds[$record->assigned_to_user_guid] = $record->assigned_to_user_guid;

            $watchers = (array) json_decode((string) $record->watchers);

            foreach ($watchers as $watcher) {
                $id           = (new UuidModel($watcher))->toBytes();
                $userIds[$id] = $id;
            }
        }

        if ($userIds) {
            $queryParams = $this->userApi->makeQueryModel();

            $queryParams->addWhere('guid', array_values($userIds));

            foreach ($this->userApi->fetchAll($queryParams) as $userModel) {
                $users[$userModel->getGuidAsBytes()] = $userModel;
            }
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

            $thisThreadItems = $threadItems[$record->guid] ?? [];

            foreach ($thisThreadItems as $threadModel) {
                $threadModel->ticket($model);

                $threadModel->user($users[$threadModel->userGuid] ?? null);

                $model->addThreadItem($threadModel);
            }

            $watchers = (array) json_decode((string) $record->watchers);

            foreach ($watchers as $watcher) {
                $id = (new UuidModel($watcher))->toBytes();

                $watcherUserModel = $users[$id] ?? null;

                if (! $watcherUserModel) {
                    continue;
                }

                $model->addWatcher($watcherUserModel);
            }

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
