<?php

declare(strict_types=1);

namespace src\app\pings\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\Ping\Ping;
use src\app\data\Ping\PingRecord;
use src\app\pings\events\PingAfterDeleteEvent;
use src\app\pings\events\PingBeforeDeleteEvent;
use src\app\pings\interfaces\PingModelInterface;

class DeletePingService
{
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(PingModelInterface $model) : void
    {
        $this->delete($model);
    }

    public function delete(PingModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new PingBeforeDeleteEvent($model));

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $this->eventDispatcher->dispatch(new PingAfterDeleteEvent($model));
    }

    private function fetchRecord(PingModelInterface $model) : PingRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(Ping::class, $params)->fetchRecord();
    }
}
