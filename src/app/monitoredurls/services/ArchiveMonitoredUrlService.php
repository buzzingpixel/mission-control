<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\MonitoredUrl\MonitoredUrl;
use src\app\datasupport\BuildQueryInterface;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterArchiveEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeArchiveEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class ArchiveMonitoredUrlService
{
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;
    private $fetchDataParamsFactory;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        FetchDataParamsFactory $fetchDataParamsFactory
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->fetchDataParamsFactory = $fetchDataParamsFactory;
    }

    public function __invoke(MonitoredUrlModelInterface $model): void
    {
        $this->archive($model);
    }

    public function archive(MonitoredUrlModelInterface $model): void
    {
        $beforeEvent = new MonitoredUrlBeforeArchiveEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $record = $this->fetchRecord($model);

        $record->is_active = 0;

        $this->ormFactory->makeOrm()->persist($record);

        $afterEvent = new MonitoredUrlAfterArchiveEvent($model);

        $this->eventDispatcher->dispatch(
            $afterEvent->provider(),
            $afterEvent->name(),
            $afterEvent
        );
    }

    private function fetchRecord(MonitoredUrlModelInterface $model): MonitoredUrlRecord
    {
        $params = $this->fetchDataParamsFactory->make();
        $params->addWhere('guid', $model->guid());
        return $this->buildQuery->build(MonitoredUrl::class, $params)->fetchRecord();
    }
}
