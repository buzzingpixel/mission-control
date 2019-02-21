<?php
declare(strict_types=1);

namespace src\app\pings\services;

use src\app\data\Server\Server;
use src\app\data\Server\ServerRecord;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\servers\events\ServerAfterDeleteEvent;
use src\app\servers\events\ServerBeforeDeleteEvent;
use src\app\servers\interfaces\ServerModelInterface;

class DeleteServerService
{
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ServerModelInterface $model): void
    {
        $this->delete($model);
    }

    public function delete(ServerModelInterface $model): void
    {
        $before = new ServerBeforeDeleteEvent($model);

        $this->eventDispatcher->dispatch($before->provider(), $before->name(), $before);

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $after = new ServerAfterDeleteEvent($model);

        $this->eventDispatcher->dispatch($after->provider(), $after->name(), $after);
    }

    private function fetchRecord(ServerModelInterface $model): ServerRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(Server::class, $params)->fetchRecord();
    }
}
