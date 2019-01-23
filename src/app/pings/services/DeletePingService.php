<?php
declare(strict_types=1);

namespace src\app\pings\services;

use src\app\data\Ping\Ping;
use src\app\data\Ping\PingRecord;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\pings\events\PingAfterDeleteEvent;
use src\app\pings\events\PingBeforeDeleteEvent;
use src\app\pings\interfaces\PingModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;

class DeletePingService
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

    public function __invoke(PingModelInterface $model): void
    {
        $this->delete($model);
    }

    public function delete(PingModelInterface $model): void
    {
        $beforeEvent = new PingBeforeDeleteEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $afterEvent = new PingAfterDeleteEvent($model);

        $this->eventDispatcher->dispatch(
            $afterEvent->provider(),
            $afterEvent->name(),
            $afterEvent
        );
    }

    private function fetchRecord(PingModelInterface $model): PingRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(Ping::class, $params)->fetchRecord();
    }
}
