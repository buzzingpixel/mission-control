<?php
declare(strict_types=1);

namespace src\app\servers;

use Psr\Container\ContainerInterface;
use src\app\servers\models\ServerModel;
use src\app\servers\models\SSHKeyModel;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
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
        // TODO: Implement ServerApi::save() method
        dd('TODO: Implement ServerApi::save() method');
    }

    public function saveSSHKey(SSHKeyModelInterface $model): void
    {
        // TODO: Implement ServerApi::saveSSHKey() method
        dd('TODO: Implement ServerApi::saveSSHKey() method');
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

    public function fetchOneSSHKey(
        ?QueryModelInterface $params = null
    ): ?SSHKeyModelInterface {
        return $this->fetchAllSSHKeys($params)[0] ?? null;
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
     * Fetches all ssh key model results based on params
     * @param QueryModelInterface $params
     * @return SSHKeyModelInterface[]
     */
    public function fetchAllSSHKeys(?QueryModelInterface $params = null): array
    {
        // TODO: Implement ServerApi::fetchAllSSHKeys() method
        dd('TODO: Implement ServerApi::fetchAllSSHKeys() method');
    }
}
