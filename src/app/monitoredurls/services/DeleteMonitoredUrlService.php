<?php

declare(strict_types=1);

namespace src\app\monitoredurls\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\MonitoredUrl\MonitoredUrl;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterDeleteEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeDeleteEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class DeleteMonitoredUrlService
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
        $this->delete($model);
    }

    public function delete(MonitoredUrlModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new MonitoredUrlBeforeDeleteEvent($model));

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $this->eventDispatcher->dispatch(new MonitoredUrlAfterDeleteEvent($model));
    }

    private function fetchRecord(MonitoredUrlModelInterface $model) : MonitoredUrlRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(MonitoredUrl::class, $params)->fetchRecord();
    }
}
