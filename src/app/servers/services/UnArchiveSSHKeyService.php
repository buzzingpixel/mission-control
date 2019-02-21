<?php
declare(strict_types=1);

namespace src\app\servers\services;

use src\app\data\SshKey\SshKey;
use src\app\data\SshKey\SshKeyRecord;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\events\SSHKeyAfterUnArchiveEvent;
use src\app\servers\events\SSHKeyBeforeUnArchiveEvent;

class UnArchiveSSHKeyService
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

    public function __invoke(SSHKeyModelInterface $model): void
    {
        $this->archive($model);
    }

    public function archive(SSHKeyModelInterface $model): void
    {
        $before = new SSHKeyBeforeUnArchiveEvent($model);

        $this->eventDispatcher->dispatch($before->provider(), $before->name(), $before);

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $after = new SSHKeyAfterUnArchiveEvent($model);

        $this->eventDispatcher->dispatch($after->provider(), $after->name(), $after);
    }

    private function fetchRecord(SSHKeyModelInterface $model): SshKeyRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(SshKey::class, $params)->fetchRecord();
    }
}
