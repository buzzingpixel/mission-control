<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use Ramsey\Uuid\UuidFactory;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as DbFactory;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
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
    private $uuidFactory;
    private $eventDispatcher;
    private $dbFactory;

    public function __construct(
        OrmFactory $ormFactory,
        UuidFactory $uuidFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        DbFactory $dbFactory
    ) {
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->uuidFactory = $uuidFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->dbFactory = $dbFactory;
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

        if (! $model->guid()) {
            $before = new MonitoredUrlIncidentBeforeSaveEvent($model, true);

            $this->eventDispatcher->dispatch($before->provider(), $before->name(), $before);

            $this->saveNew($model);

            $after = new MonitoredUrlIncidentAfterSaveEvent($model, true);

            $this->eventDispatcher->dispatch($after->provider(), $after->name(), $after);

            return;
        }

        $before = new MonitoredUrlIncidentBeforeSaveEvent($model, false);

        $this->eventDispatcher->dispatch($before->provider(), $before->name(), $before);

        $this->saveExisting($model);

        $after = new MonitoredUrlIncidentAfterSaveEvent($model, false);

        $this->eventDispatcher->dispatch($after->provider(), $after->name(), $after);
    }

    private function saveNew(MonitoredUrlIncidentModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(MonitoredUrlIncident::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->guid = $this->uuidFactory->uuid4()->toString();

        $this->finalSave($model, $record);
    }

    private function saveExisting(MonitoredUrlIncidentModelInterface $model): void
    {
        $params = $this->dbFactory->makeQueryModel();
        $params->limit(1);
        $params->addWhere('guid', $model->guid());

        $this->finalSave(
            $model,
            $this->buildQuery->build(MonitoredUrlIncident::class, $params)->fetchRecord()
        );
    }

    private function finalSave(
        MonitoredUrlIncidentModelInterface $model,
        MonitoredUrlIncidentRecord $record
    ): void {
        $record->monitored_url_guid = $model->monitoredUrlGuid();
        $record->event_type = $model->eventType();
        $record->status_code = $model->statusCode();
        $record->message = $model->message();
        $record->event_at = $model->eventAt()->format('Y-m-d H:i:s');
        $record->event_at_time_zone = $model->eventAt()->getTimezone()->getName();

        $this->ormFactory->makeOrm()->persist($record);
    }
}
