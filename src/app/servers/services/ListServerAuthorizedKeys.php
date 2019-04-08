<?php

declare(strict_types=1);

namespace src\app\servers\services;

use LogicException;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\utilities\RSAFactory;
use src\app\utilities\SSH2Factory;
use function explode;
use function trim;

class ListServerAuthorizedKeys
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

    /**
     * @return string[]
     */
    public function __invoke(ServerModelInterface $server) : array
    {
        return $this->list($server);
    }

    /**
     * @return string[]
     */
    public function list(ServerModelInterface $server) : array
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

        $keysString = trim($ssh->exec('cat ~/.ssh/authorized_keys'));

        return explode("\n", $keysString);
    }
}
