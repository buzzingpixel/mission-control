<?php
declare(strict_types=1);

namespace src\app\pings\services;

use src\app\data\SshKey\SshKey;
use src\app\data\SshKey\SshKeyRecord;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\servers\events\SSHKeyAfterDeleteEvent;
use src\app\servers\events\SSHKeyBeforeDeleteEvent;
use src\app\servers\interfaces\SSHKeyModelInterface;

class DeleteSSHKeyService
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
        $this->delete($model);
    }

    public function delete(SSHKeyModelInterface $model): void
    {
        $before = new SSHKeyBeforeDeleteEvent($model);

        $this->eventDispatcher->dispatch($before->provider(), $before->name(), $before);

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $after = new SSHKeyAfterDeleteEvent($model);

        $this->eventDispatcher->dispatch($after->provider(), $after->name(), $after);
    }

    private function fetchRecord(SSHKeyModelInterface $model): SshKeyRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(SshKey::class, $params)->fetchRecord();
    }
}
