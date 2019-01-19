<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use DateTime;
use DateTimeZone;
use src\app\data\MonitoredUrl\MonitoredUrl;
use src\app\datasupport\BuildQueryInterface;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\datasupport\FetchDataParamsInterface;
use src\app\monitoredurls\models\MonitoredUrlModel;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class FetchMonitoredUrlsService
{
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return MonitoredUrlModelInterface[]
     */
    public function __invoke(FetchDataParamsInterface $params): array
    {
        return $this->fetch($params);
    }

    /**
     * @return MonitoredUrlModelInterface[]
     */
    public function fetch(FetchDataParamsInterface $params): array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new MonitoredUrlModel();

            $model->guid($record->guid);
            $model->projectGuid($record->project_guid);
            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->title($record->title);
            $model->slug($record->slug);
            $model->url($record->url);
            $model->pendingError($record->pending_error === 1 || $record->pending_error === '1');
            $model->hasError($record->has_error === 1 || $record->pending_error === '1');
            /** @noinspection PhpUnhandledExceptionInspection */
            $model->checkedAt(new DateTime(
                $record->checked_at,
                new DateTimeZone($record->checked_at_time_zone)
            ));
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
     * @return MonitoredUrlRecord[]
     */
    private function fetchResults($params): array
    {
        return $this->buildQuery->build(MonitoredUrl::class, $params)->fetchRecords();
    }
}
