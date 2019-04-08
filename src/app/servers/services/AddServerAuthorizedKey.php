<?php

declare(strict_types=1);

namespace src\app\servers\services;

use src\app\servers\interfaces\ServerModelInterface;

class AddServerAuthorizedKey
{
    /** @var GetLoggedInServerSshConnection */
    private $getConnection;

    public function __construct(GetLoggedInServerSshConnection $getConnection)
    {
        $this->getConnection = $getConnection;
    }

    public function __invoke(string $key, ServerModelInterface $server) : void
    {
        $this->add($key, $server);
    }

    public function add(string $key, ServerModelInterface $server) : void
    {
        $ssh = $this->getConnection->get($server);

        $keyExists = ! empty($ssh->exec('grep "' . $key . '" ~/.ssh/authorized_keys;'));

        if ($keyExists) {
            return;
        }

        $ssh->exec('echo "' . $key . '" >> ~/.ssh/authorized_keys;');
    }
}
