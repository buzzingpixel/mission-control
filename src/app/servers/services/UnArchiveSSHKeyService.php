<?php

declare(strict_types=1);

namespace src\app\servers\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\SshKey\SshKey;
use src\app\data\SshKey\SshKeyRecord;
use src\app\servers\events\SSHKeyAfterUnArchiveEvent;
use src\app\servers\events\SSHKeyBeforeUnArchiveEvent;
use src\app\servers\interfaces\SSHKeyModelInterface;

class UnArchiveSSHKeyService
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
        $this->unArchive($model);
    }

    public function unArchive(SSHKeyModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new SSHKeyBeforeUnArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new SSHKeyAfterUnArchiveEvent($model));
    }

    private function fetchRecord(SSHKeyModelInterface $model) : SshKeyRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(SshKey::class, $params)->fetchRecord();
    }
}
