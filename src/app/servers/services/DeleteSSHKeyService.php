<?php

declare(strict_types=1);

namespace src\app\pings\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\SshKey\SshKey;
use src\app\data\SshKey\SshKeyRecord;
use src\app\servers\events\SSHKeyAfterDeleteEvent;
use src\app\servers\events\SSHKeyBeforeDeleteEvent;
use src\app\servers\interfaces\SSHKeyModelInterface;

class DeleteSSHKeyService
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
        $this->delete($model);
    }

    public function delete(SSHKeyModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new SSHKeyBeforeDeleteEvent($model));

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $this->eventDispatcher->dispatch(new SSHKeyAfterDeleteEvent($model));
    }

    private function fetchRecord(SSHKeyModelInterface $model) : SshKeyRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(SshKey::class, $params)->fetchRecord();
    }
}
