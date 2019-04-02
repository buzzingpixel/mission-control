<?php

declare(strict_types=1);

namespace src\app\monitoredurls\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;
use Throwable;

class ProjectUnArchiveListener implements EventListenerInterface
{
    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;

    public function __construct(MonitoredUrlsApiInterface $monitoredUrlsApi)
    {
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    /**
     * @throws Throwable
     */
    public function call(EventInterface $event) : void
    {
        /** @var ProjectBeforeUnArchiveEvent $event */

        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->monitoredUrlsApi->fetchAll($queryModel) as $model) {
            if ($model->isActive()) {
                continue;
            }

            $model->isActive(true);
            $this->monitoredUrlsApi->save($model);
        }
    }
}
