<?php

declare(strict_types=1);

namespace src\app\monitoredurls\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\MonitoredUrl\MonitoredUrl;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterUnArchiveEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeUnArchiveEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class UnArchiveMonitoredUrlService
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
        $this->unArchive($model);
    }

    public function unArchive(MonitoredUrlModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new MonitoredUrlBeforeUnArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new MonitoredUrlAfterUnArchiveEvent($model));
    }

    private function fetchRecord(MonitoredUrlModelInterface $model) : MonitoredUrlRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(MonitoredUrl::class, $params)->fetchRecord();
    }
}
