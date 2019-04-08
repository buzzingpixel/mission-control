<?php

declare(strict_types=1);

namespace src\app\servers\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use Throwable;
use function array_walk;

class ProjectUnArchiveListener implements EventListenerInterface
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

    private function respond(ProjectBeforeUnArchiveEvent $event) : void
    {
        $queryModel = $this->serverApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        $related = $this->serverApi->fetchAll($queryModel);

        array_walk($related, [$this, 'unArchive']);
    }

    /**
     * @throws Throwable
     */
    private function unArchive(ServerModelInterface $model) : void
    {
        if ($model->isActive()) {
            return;
        }

        $model->isActive(true);

        $this->serverApi->save($model);
    }
}
