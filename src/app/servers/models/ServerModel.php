<?php

declare(strict_types=1);

namespace src\app\servers\models;

use src\app\servers\interfaces\RemoteServiceAdapterInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\support\traits\StandardModelTrait;

class ServerModel implements ServerModelInterface
{
    use StandardModelTrait;

    /** @var ?RemoteServiceAdapterInterface */
    private $remoteServiceAdapter;

    public function remoteServiceAdapter(
        ?RemoteServiceAdapterInterface $val = null
    ) : ?RemoteServiceAdapterInterface {
        return $this->remoteServiceAdapter = $val ?? $this->remoteServiceAdapter;
    }

    public function clearRemoteServiceAdapter() : void
    {
        $this->remoteServiceAdapter = null;
    }

    /** @var ?string */
    private $remoteId;

    public function remoteId(?string $val = null) : ?string
    {
        return $this->remoteId = $val ?? $this->remoteId;
    }

    /** @var ?string */
    private $address;

    public function address(?string $val = null) : ?string
    {
        return $this->address = $val ?? $this->address;
    }

    /** @var ?int */
    private $sshPort;

    public function sshPort(?int $val = null) : ?int
    {
        return $this->sshPort = $val ?? $this->sshPort;
    }

    /** @var ?SSHKeyModelInterface */
    private $sshKeyModel;

    public function sshKeyModel(
        ?SSHKeyModelInterface $val = null
    ) : ?SSHKeyModelInterface {
        return $this->sshKeyModel = $val ?? $this->sshKeyModel;
    }

    public function clearSSHKeyModel() : void
    {
        $this->sshKeyModel = null;
    }

    /** @var ?string */
    private $sshUserName;

    public function sshUserName(?string $val = null) : ?string
    {
        return $this->sshUserName = $val ?? $this->sshUserName;
    }
}
