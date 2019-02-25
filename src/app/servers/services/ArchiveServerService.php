<?php
declare(strict_types=1);

namespace src\app\servers\services;

use src\app\data\Server\Server;
use src\app\data\Server\ServerRecord;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\servers\events\ServerAfterArchiveEvent;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\events\ServerBeforeArchiveEvent;

class ArchiveServerService
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
        $this->archive($model);
    }

    public function archive(ServerModelInterface $model): void
    {
        $this->eventDispatcher->dispatch(new ServerAfterArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 0;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new ServerBeforeArchiveEvent($model));
    }

    private function fetchRecord(ServerModelInterface $model): ServerRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(Server::class, $params)->fetchRecord();
    }
}
