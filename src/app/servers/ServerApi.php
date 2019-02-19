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
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\interfaces\ServerModelInterface;

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
        // TODO: Implement ServerApi::archive() method
        dd('TODO: Implement ServerApi::archive() method');
    }

    public function archiveSSHKey(SSHKeyModelInterface $model): void
    {
        // TODO: Implement ServerApi::archiveSSHKey() method
        dd('TODO: Implement ServerApi::archiveSSHKey() method');
    }

    public function unArchive(ServerModelInterface $model): void
    {
        // TODO: Implement ServerApi::unArchive() method
        dd('TODO: Implement ServerApi::unArchive() method');
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

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ServerModelInterface {
        return $this->fetchAll($params)[0] ?? null;
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
        // TODO: Implement ServerApi::fetchAll() method
        dd('TODO: Implement ServerApi::fetchAll() method');
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
