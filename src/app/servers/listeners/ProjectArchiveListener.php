<?php

declare(strict_types=1);

namespace src\app\servers\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use Throwable;
use function array_walk;

class ProjectArchiveListener implements EventListenerInterface
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

    private function respond(ProjectBeforeArchiveEvent $event) : void
    {
        $queryModel = $this->serverApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        $related = $this->serverApi->fetchAll($queryModel);

        array_walk($related, [$this, 'archive']);
    }

    /**
     * @throws Throwable
     */
    private function archive(ServerModelInterface $model) : void
    {
        if (! $model->isActive()) {
            return;
        }

        $model->isActive(false);

        $this->serverApi->save($model);
    }
}
