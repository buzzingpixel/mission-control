<?php
declare(strict_types=1);

namespace src\app\pings\listeners;

use src\app\pings\interfaces\PingApiInterface;
use corbomite\events\interfaces\EventInterface;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use corbomite\events\interfaces\EventListenerInterface;

class ProjectArchiveListener implements EventListenerInterface
{
    private $pingApi;

    public function __construct(PingApiInterface $pingApi)
    {
        $this->pingApi = $pingApi;
    }

    public function call(EventInterface $event): void
    {
        /** @var ProjectBeforeArchiveEvent $event */

        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->pingApi->fetchAll($queryModel) as $model) {
            if (! $model->isActive()) {
                continue;
            }

            $model->isActive(false);
            $this->pingApi->save($model);
        }
    }
}
