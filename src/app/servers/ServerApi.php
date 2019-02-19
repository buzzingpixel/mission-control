<?php
declare(strict_types=1);

namespace src\app\servers;

use Psr\Container\ContainerInterface;
use src\app\servers\models\ServerModel;
use src\app\servers\models\SSHKeyModel;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\servers\services\SaveServerService;
use src\app\servers\services\SaveSSHKeyService;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\servers\services\FetchSSHKeyService;
use src\app\servers\services\FetchServerService;
use src\app\servers\services\ArchiveServerService;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\services\ArchiveSSHKeyService;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\services\UnArchiveServerService;

class ServerApi implements ServerApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createModel(): ServerModelInterface
    {
        return new ServerModel();
    }

    public function createSShKeyModel(): SSHKeyModelInterface
    {
        return new SSHKeyModel();
    }

    public function save(ServerModelInterface $model): void
    {
        $service = $this->di->get(SaveServerService::class);
        $service->save($model);
    }

    public function saveSSHKey(SSHKeyModelInterface $model): void
    {
        $service = $this->di->get(SaveSSHKeyService::class);
        $service->save($model);
    }

    public function archive(ServerModelInterface $model): void
    {
        $service = $this->di->get(ArchiveServerService::class);
        $service->archive($model);
    }

    public function archiveSSHKey(SSHKeyModelInterface $model): void
    {
        $service = $this->di->get(ArchiveSSHKeyService::class);
        $service->archive($model);
    }

    public function unArchive(ServerModelInterface $model): void
    {
        $service = $this->di->get(UnArchiveServerService::class);
        $service->unArchive($model);
    }

    public function unArchiveSSHKey(SSHKeyModelInterface $model): void
    {
        // TODO: Implement ServerApi::unArchiveSSHKey() method
        dd('TODO: Implement ServerApi::unArchiveSSHKey() method');
    }

    public function delete(ServerModelInterface $model): void
    {
        // TODO: Implement ServerApi::delete() method
        dd('TODO: Implement ServerApi::delete() method');
    }

    public function deleteSSHKey(SSHKeyModelInterface $model): void
    {
        // TODO: Implement ServerApi::deleteSSHKey() method
        dd('TODO: Implement ServerApi::deleteSSHKey() method');
    }

    private $serverLimit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ServerModelInterface {
        $this->serverLimit = 1;
        $result = $this->fetchAll($params)[0] ?? null;
        $this->serverLimit = null;
        return $result;
    }

    private $sshKeyLimit;

    public function fetchOneSSHKey(
        ?QueryModelInterface $params = null
    ): ?SSHKeyModelInterface {
        $this->sshKeyLimit = 1;
        $result = $this->fetchAllSSHKeys($params)[0] ?? null;
        $this->sshKeyLimit = null;
        return $result;
    }

    /**
     * Fetches all server model results based on params
     * @param QueryModelInterface $params
     * @return ServerModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->get(FetchServerService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        if ($this->serverLimit) {
            $params->limit($this->serverLimit);
        }

        return $service->fetch($params);
    }

    /**
     * @return SSHKeyModelInterface[]
     */
    public function fetchAllSSHKeys(?QueryModelInterface $params = null): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->get(FetchSSHKeyService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        if ($this->sshKeyLimit) {
            $params->limit($this->sshKeyLimit);
        }

        return $service->fetch($params);
    }
}
