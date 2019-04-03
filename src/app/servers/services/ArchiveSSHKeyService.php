<?php

declare(strict_types=1);

namespace src\app\servers\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\SshKey\SshKey;
use src\app\data\SshKey\SshKeyRecord;
use src\app\servers\events\SSHKeyAfterArchiveEvent;
use src\app\servers\events\SSHKeyBeforeArchiveEvent;
use src\app\servers\interfaces\SSHKeyModelInterface;

class ArchiveSSHKeyService
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

    public function __invoke(SSHKeyModelInterface $model) : void
    {
        $this->archive($model);
    }

    public function archive(SSHKeyModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new SSHKeyAfterArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 0;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new SSHKeyBeforeArchiveEvent($model));
    }

    private function fetchRecord(SSHKeyModelInterface $model) : SshKeyRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(SshKey::class, $params)->fetchRecord();
    }
}
