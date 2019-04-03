<?php

declare(strict_types=1);

namespace src\app\pings\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\pings\interfaces\PingApiInterface;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;

class ProjectUnArchiveListener implements EventListenerInterface
{
    /** @var PingApiInterface */
    private $pingApi;

    public function __construct(PingApiInterface $pingApi)
    {
        $this->pingApi = $pingApi;
    }

    public function call(EventInterface $event) : void
    {
        /** @var ProjectBeforeUnArchiveEvent $event */

        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->pingApi->fetchAll($queryModel) as $model) {
            if ($model->isActive()) {
                continue;
            }

            $model->isActive(true);
            $this->pingApi->save($model);
        }
    }
}
