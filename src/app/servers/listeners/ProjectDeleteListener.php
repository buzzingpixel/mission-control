<?php

declare(strict_types=1);

namespace src\app\servers\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use function array_walk;

class ProjectDeleteListener implements EventListenerInterface
{
    /** @var ServerApiInterface */
    private $serverApi;

    public function __construct(ServerApiInterface $serverApi)
    {
        $this->serverApi = $serverApi;
    }

    public function call(EventInterface $event) : void
    {
        /** @noinspection PhpParamsInspection */
        $this->respond($event);
    }

    private function respond(ProjectBeforeDeleteEvent $event) : void
    {
        $queryModel = $this->serverApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        $relatedPipelines = $this->serverApi->fetchAll($queryModel);

        array_walk($relatedPipelines, [$this, 'delete']);
    }

    private function delete(ServerModelInterface $model) : void
    {
        $this->serverApi->delete($model);
    }
}
