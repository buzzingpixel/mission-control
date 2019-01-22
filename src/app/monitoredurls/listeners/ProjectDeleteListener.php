<?php
declare(strict_types=1);

namespace src\app\monitoredurls\listeners;

use corbomite\events\interfaces\EventInterface;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class ProjectDeleteListener implements EventListenerInterface
{
    private $monitoredUrlsApi;

    public function __construct(MonitoredUrlsApiInterface $monitoredUrlsApi)
    {
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    public function call(EventInterface $event): void
    {
        /** @var ProjectBeforeDeleteEvent $event */

        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->guid());

        foreach ($this->monitoredUrlsApi->fetchAll($queryModel) as $model) {
            $this->monitoredUrlsApi->delete($model);
        }
    }
}
