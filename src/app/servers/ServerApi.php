<?php

declare(strict_types=1);

namespace src\app\servers;

use corbomite\db\interfaces\QueryModelInterface;
use Psr\Container\ContainerInterface;
use src\app\pings\services\DeleteServerService;
use src\app\pings\services\DeleteSSHKeyService;
use src\app\servers\interfaces\ServerApiInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\models\ServerModel;
use src\app\servers\models\SSHKeyModel;
use src\app\servers\services\AddServerAuthorizedKey;
use src\app\servers\services\ArchiveServerService;
use src\app\servers\services\ArchiveSSHKeyService;
use src\app\servers\services\FetchServerService;
use src\app\servers\services\FetchSSHKeyService;
use src\app\servers\services\GenerateSSHKeyService;
use src\app\servers\services\ListServerAuthorizedKeys;
use src\app\servers\services\RemoveServerAuthorizedKey;
use src\app\servers\services\SaveServerService;
use src\app\servers\services\SaveSSHKeyService;
use src\app\servers\services\UnArchiveServerService;
use src\app\servers\services\UnArchiveSSHKeyService;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\support\traits\UuidToBytesTrait;

class ServerApi implements ServerApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createModel() : ServerModelInterface
    {
        return new ServerModel();
    }

    public function createSShKeyModel() : SSHKeyModelInterface
    {
        return new SSHKeyModel();
    }

    public function save(ServerModelInterface $model) : void
    {
        /** @var SaveServerService $service */
        $service = $this->di->get(SaveServerService::class);
        $service->save($model);
    }

    public function saveSSHKey(SSHKeyModelInterface $model) : void
    {
        $service = $this->di->get(SaveSSHKeyService::class);
        $service->save($model);
    }

    public function archive(ServerModelInterface $model) : void
    {
        $service = $this->di->get(ArchiveServerService::class);
        $service->archive($model);
    }

    public function archiveSSHKey(SSHKeyModelInterface $model) : void
    {
        $service = $this->di->get(ArchiveSSHKeyService::class);
        $service->archive($model);
    }

    public function unArchive(ServerModelInterface $model) : void
    {
        $service = $this->di->get(UnArchiveServerService::class);
        $service->unArchive($model);
    }

    public function unArchiveSSHKey(SSHKeyModelInterface $model) : void
    {
        $service = $this->di->get(UnArchiveSSHKeyService::class);
        $service->unArchive($model);
    }

    public function delete(ServerModelInterface $model) : void
    {
        $service = $this->di->get(DeleteServerService::class);
        $service->delete($model);
    }

    public function deleteSSHKey(SSHKeyModelInterface $model) : void
    {
        $service = $this->di->get(DeleteSSHKeyService::class);
        $service->delete($model);
    }

    /** @var ?int */
    private $serverLimit;

    public function fetchOne(?QueryModelInterface $params = null) : ?ServerModelInterface
    {
        $this->serverLimit = 1;
        $result            = $this->fetchAll($params)[0] ?? null;
        $this->serverLimit = null;

        return $result;
    }

    /** @var ?int */
    private $sshKeyLimit;

    public function fetchOneSSHKey(?QueryModelInterface $params = null) : ?SSHKeyModelInterface
    {
        $this->sshKeyLimit = 1;
        $result            = $this->fetchAllSSHKeys($params)[0] ?? null;
        $this->sshKeyLimit = null;

        return $result;
    }

    /**
     * Fetches all server model results based on params
     *
     * @return ServerModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array
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
    public function fetchAllSSHKeys(?QueryModelInterface $params = null) : array
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

    /**
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     *
     * @return string[] [
     *     'server-slug' => 'Server Name',
     *     'another-server-slug' => 'Another Server Name',
     * ]
     */
    public function fetchAsSelectArray(?QueryModelInterface $params = null, bool $keyIsSlug = false) : array
    {
        $models = $this->fetchAll($params);

        $items = [];

        foreach ($models as $model) {
            $key         = $keyIsSlug ? $model->slug() : $model->guid();
            $items[$key] = $model->title();
        }

        return $items;
    }

    /**
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     *
     * @return string[] [
     *     'ssh-key-slug' => 'SSH Key Name',
     *     'another-ssh-key-slug' => 'Another SSH Key Name',
     * ]
     */
    public function fetchSSHKeysAsSelectArray(?QueryModelInterface $params = null, bool $keyIsSlug = false) : array
    {
        $models = $this->fetchAllSSHKeys($params);

        $items = [];

        foreach ($models as $model) {
            $key         = $keyIsSlug ? $model->slug() : $model->guid();
            $items[$key] = $model->title();
        }

        return $items;
    }

    /**
     * @return string[]
     */
    public function generateSSHKey() : array
    {
        $service = $this->di->get(GenerateSSHKeyService::class);

        return $service->generate();
    }

    /**
     * @return string[]
     */
    public function listServerAuthorizedKeys(ServerModelInterface $model) : array
    {
        return $this->di->get(ListServerAuthorizedKeys::class)->list($model);
    }

    public function addServerAuthorizedKey(string $key, ServerModelInterface $model) : void
    {
        $this->di->get(AddServerAuthorizedKey::class)->add($key, $model);
    }

    public function removeServerAuthorizedKey(string $key, ServerModelInterface $model) : void
    {
        $this->di->get(RemoveServerAuthorizedKey::class)->remove($key, $model);
    }
}
