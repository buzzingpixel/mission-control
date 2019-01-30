<?php
declare(strict_types=1);

namespace src\app\pings\services;

use DateTime;
use DateTimeZone;
use src\app\data\Ping\Ping;
use src\app\data\Ping\PingRecord;
use src\app\pings\models\PingModel;
use src\app\pings\interfaces\PingModelInterface;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;

class FetchPingService
{
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return PingModelInterface[]
     */
    public function __invoke(QueryModelInterface $params): array
    {
        return $this->fetch($params);
    }

    /**
     * @return PingModelInterface[]
     */
    public function fetch(QueryModelInterface $params): array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new PingModel();

            $model->setGuidAsBytes($record->guid);
            if ($record->project_guid) {
                $model->setProjectGuidAsBytes($record->project_guid);
            }
            $model->pingId($record->ping_id);
            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->title($record->title);
            $model->slug($record->slug);
            $model->pendingError($record->pending_error === 1 || $record->pending_error === '1');
            $model->hasError($record->has_error === 1 || $record->pending_error === '1');
            $model->expectEvery($record->expect_every);
            $model->warnAfter($record->warn_after);

            if ($lastPingAt = $record->last_ping_at) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->lastPingAt(new DateTime(
                    $lastPingAt,
                    new DateTimeZone($record->last_ping_at_time_zone)
                ));
            }

            if ($lastNotificationAt = $record->last_notification_at) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->lastNotificationAt(new DateTime(
                    $lastNotificationAt,
                    new DateTimeZone($record->last_notification_at_time_zone)
                ));
            }

            /** @noinspection PhpUnhandledExceptionInspection */
            $model->addedAt(new DateTime(
                $record->added_at,
                new DateTimeZone($record->added_at_time_zone)
            ));

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param $params
     * @return PingRecord[]
     */
    private function fetchResults($params): array
    {
        return $this->buildQuery->build(Ping::class, $params)->fetchRecords();
    }
}
