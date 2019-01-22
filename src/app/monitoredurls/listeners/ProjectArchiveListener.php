<?php
declare(strict_types=1);

namespace src\app\monitoredurls\listeners;

use corbomite\events\interfaces\EventInterface;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class ProjectArchiveListener implements EventListenerInterface
{
    private $monitoredUrlsApi;

    public function __construct(MonitoredUrlsApiInterface $monitoredUrlsApi)
    {
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    public function call(EventInterface $event): void
    {
        /** @var ProjectBeforeArchiveEvent $event */

        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->guid());

        foreach ($this->monitoredUrlsApi->fetchAll($queryModel) as $model) {
            if (! $model->isActive()) {
                continue;
            }

            $model->isActive(false);
            $this->monitoredUrlsApi->save($model);
        }
    }
}
