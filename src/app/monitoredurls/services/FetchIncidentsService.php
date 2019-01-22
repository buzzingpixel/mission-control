<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use DateTime;
use DateTimeZone;
use corbomite\db\interfaces\QueryModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\monitoredurls\models\MonitoredUrlIncidentModel;
use src\app\data\MonitoredUrlIncident\MonitoredUrlIncident;
use src\app\data\MonitoredUrlIncident\MonitoredUrlIncidentRecord;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;

class FetchIncidentsService
{
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return MonitoredUrlIncidentModelInterface[]
     */
    public function __invoke(QueryModelInterface $params): array
    {
        return $this->fetch($params);
    }

    /**
     * @return MonitoredUrlIncidentModelInterface[]
     */
    public function fetch(QueryModelInterface $params): array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new MonitoredUrlIncidentModel();

            $model->guid($record->guid);
            $model->monitoredUrlGuid($record->monitored_url_guid);
            $model->eventType($record->event_type);
            $model->statusCode($record->status_code);
            $model->message($record->message);
            /** @noinspection PhpUnhandledExceptionInspection */
            $model->eventAt(new DateTime(
                $record->event_at,
                new DateTimeZone($record->event_at_time_zone)
            ));

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param $params
     * @return MonitoredUrlIncidentRecord[]
     */
    private function fetchResults($params): array
    {
        return $this->buildQuery->build(MonitoredUrlIncident::class, $params)->fetchRecords();
    }
}
