<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\MonitoredUrl\MonitoredUrl;
use src\app\datasupport\BuildQueryInterface;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterDeleteEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeDeleteEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class DeleteMonitoredUrlService
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
        $this->delete($model);
    }

    public function delete(MonitoredUrlModelInterface $model): void
    {
        $beforeEvent = new MonitoredUrlBeforeDeleteEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $afterEvent = new MonitoredUrlAfterDeleteEvent($model);

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
