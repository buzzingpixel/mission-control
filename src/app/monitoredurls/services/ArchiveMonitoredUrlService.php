<?php

declare(strict_types=1);

namespace src\app\monitoredurls\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\MonitoredUrl\MonitoredUrl;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterArchiveEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeArchiveEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class ArchiveMonitoredUrlService
{
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(MonitoredUrlModelInterface $model) : void
    {
        $this->archive($model);
    }

    public function archive(MonitoredUrlModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new MonitoredUrlBeforeArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 0;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new MonitoredUrlAfterArchiveEvent($model));
    }

    private function fetchRecord(MonitoredUrlModelInterface $model) : MonitoredUrlRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(MonitoredUrl::class, $params)->fetchRecord();
    }
}
