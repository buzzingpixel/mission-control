<?php
declare(strict_types=1);

namespace src\app\pings\services;

use src\app\data\Ping\Ping;
use src\app\data\Ping\PingRecord;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\pings\interfaces\PingModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\pings\events\PingAfterUnArchiveEvent;
use src\app\pings\events\PingBeforeUnArchiveEvent;

class UnArchivePingService
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
        $this->unArchive($model);
    }

    public function unArchive(PingModelInterface $model): void
    {
        $beforeEvent = new PingBeforeUnArchiveEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $afterEvent = new PingAfterUnArchiveEvent($model);

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
