<?php

declare(strict_types=1);

namespace src\app\servers\services;

use LogicException;
use phpseclib\Net\SSH2;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\utilities\RSAFactory;
use src\app\utilities\SSH2Factory;

class GetLoggedInServerSshConnection
{
    /** @var SSH2Factory */
    private $ssh2Factory;
    /** @var RSAFactory */
    private $rsaFactory;

    public function __construct(SSH2Factory $ssh2Factory, RSAFactory $rsaFactory)
    {
        $this->ssh2Factory = $ssh2Factory;
        $this->rsaFactory  = $rsaFactory;
    }

    public function __invoke(ServerModelInterface $server) : SSH2
    {
        return $this->get($server);
    }

    public function get(ServerModelInterface $server) : SSH2
    {
        $ssh = $this->ssh2Factory->make(
            $server->address(),
            $server->sshPort()
        );

        $sshKeyModel = $server->sshKeyModel();

        $key = $this->rsaFactory->make(
            $sshKeyModel->private(),
            $sshKeyModel->public()
        );

        if (! $ssh->login($server->sshUserName(), $key)) {
            // dd($ssh->getErrors());
            throw new LogicException('Unable to log in to SSH Server');
        }

        return $ssh;
    }
}
