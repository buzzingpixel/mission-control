<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\MonitoredUrl\MonitoredUrl;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterDeleteEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeDeleteEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class DeleteMonitoredUrlService
{
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(MonitoredUrlModelInterface $model): void
    {
        $this->delete($model);
    }

    public function delete(MonitoredUrlModelInterface $model): void
    {
        $this->eventDispatcher->dispatch(new MonitoredUrlBeforeDeleteEvent($model));

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $this->eventDispatcher->dispatch(new MonitoredUrlAfterDeleteEvent($model));
    }

    private function fetchRecord(MonitoredUrlModelInterface $model): MonitoredUrlRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(MonitoredUrl::class, $params)->fetchRecord();
    }
}
