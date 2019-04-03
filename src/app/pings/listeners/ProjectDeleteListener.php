<?php

declare(strict_types=1);

namespace src\app\pings\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\pings\interfaces\PingApiInterface;
use src\app\projects\events\ProjectBeforeDeleteEvent;

class ProjectDeleteListener implements EventListenerInterface
{
    /** @var PingApiInterface */
    private $pingApi;

    public function __construct(PingApiInterface $pingApi)
    {
        $this->pingApi = $pingApi;
    }

    public function call(EventInterface $event) : void
    {
        /** @var ProjectBeforeDeleteEvent $event */

        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->pingApi->fetchAll($queryModel) as $model) {
            $this->pingApi->delete($model);
        }
    }
}
