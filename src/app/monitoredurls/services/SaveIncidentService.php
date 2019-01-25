<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use DateTimeZone;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use Atlas\Table\Exception as AtlasTableException;
use src\app\data\MonitoredUrlIncident\MonitoredUrlIncident;
use src\app\data\MonitoredUrlIncident\MonitoredUrlIncidentRecord;
use src\app\monitoredurls\events\MonitoredUrlIncidentAfterSaveEvent;
use src\app\monitoredurls\events\MonitoredUrlIncidentBeforeSaveEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlIncidentModelException;

class SaveIncidentService
{
    private $ormFactory;
    private $buildQuery;
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidMonitoredUrlIncidentModelException
     */
    public function __invoke(MonitoredUrlIncidentModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidMonitoredUrlIncidentModelException
     */
    public function save(MonitoredUrlIncidentModelInterface $model): void
    {
        if (! $model->monitoredUrlGuid() ||
            ! $model->eventType()
        ) {
            throw new InvalidMonitoredUrlIncidentModelException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(MonitoredUrlIncident::class, $fetchModel)->fetchRecord();

        if (! $existingRecord) {
            $before = new MonitoredUrlIncidentBeforeSaveEvent($model, true);

            $this->eventDispatcher->dispatch($before->provider(), $before->name(), $before);

            $this->saveNew($model);

            $after = new MonitoredUrlIncidentAfterSaveEvent($model, true);

            $this->eventDispatcher->dispatch($after->provider(), $after->name(), $after);

            return;
        }

        $before = new MonitoredUrlIncidentBeforeSaveEvent($model, false);

        $this->eventDispatcher->dispatch($before->provider(), $before->name(), $before);

        $this->finalSave($model, $existingRecord);

        $after = new MonitoredUrlIncidentAfterSaveEvent($model, false);

        $this->eventDispatcher->dispatch($after->provider(), $after->name(), $after);
    }

    private function saveNew(MonitoredUrlIncidentModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(MonitoredUrlIncident::class);

        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        MonitoredUrlIncidentModelInterface $model,
        MonitoredUrlIncidentRecord $record
    ): void {
        $eventAt = $model->eventAt();
        $lastNotificationAt = $model->lastNotificationAt();

        $eventAt->setTimezone(new DateTimeZone('UTC'));

        $record->monitored_url_guid = $model->getMonitoredUrlGuidAsBytes();
        $record->event_type = $model->eventType();
        $record->status_code = $model->statusCode();
        $record->message = $model->message();
        $record->event_at = $eventAt->format('Y-m-d H:i:s');
        $record->event_at_time_zone = $eventAt->getTimezone()->getName();

        if ($lastNotificationAt) {
            $lastNotificationAt->setTimezone(new DateTimeZone('UTC'));
            $record->last_notification_at = $lastNotificationAt->format('Y-m-d H:i:s');
            $record->last_notification_at_time_zone = $lastNotificationAt->getTimezone()->getName();
        }

        if (! $lastNotificationAt) {
            $record->last_notification_at = null;
            $record->last_notification_at_time_zone = null;
        }

        try {
            $this->ormFactory->makeOrm()->persist($record);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() === 'Expected 1 row affected, actual 0.') {
                return;
            }

            throw $e;
        }
    }
}
