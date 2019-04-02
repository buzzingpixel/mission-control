<?php

declare(strict_types=1);

namespace src\app\monitoredurls\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use DateTime;
use DateTimeZone;
use src\app\data\MonitoredUrlIncident\MonitoredUrlIncident;
use src\app\data\MonitoredUrlIncident\MonitoredUrlIncidentRecord;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;
use src\app\monitoredurls\models\MonitoredUrlIncidentModel;

class FetchIncidentsService
{
    /** @var BuildQueryInterface */
    private $buildQuery;

    public function __construct(BuildQueryInterface $buildQuery)
    {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return MonitoredUrlIncidentModelInterface[]
     */
    public function __invoke(QueryModelInterface $params) : array
    {
        return $this->fetch($params);
    }

    /**
     * @return MonitoredUrlIncidentModelInterface[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new MonitoredUrlIncidentModel();

            $model->setGuidAsBytes($record->guid);
            $model->setMonitoredUrlGuidAsBytes($record->monitored_url_guid);
            $model->eventType($record->event_type);
            $model->statusCode($record->status_code);
            $model->message($record->message);
            /** @noinspection PhpUnhandledExceptionInspection */
            $model->eventAt(new DateTime(
                $record->event_at,
                new DateTimeZone($record->event_at_time_zone)
            ));

            if ($record->last_notification_at) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->lastNotificationAt(new DateTime(
                    $record->last_notification_at,
                    new DateTimeZone($record->event_at_time_zone)
                ));
            }

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return MonitoredUrlIncidentRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery
            ->build(MonitoredUrlIncident::class, $params)
            ->fetchRecords();
    }
}
