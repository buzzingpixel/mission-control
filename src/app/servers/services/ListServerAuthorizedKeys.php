<?php

declare(strict_types=1);

namespace src\app\servers\services;

use src\app\servers\interfaces\ServerModelInterface;
use function explode;
use function trim;

class ListServerAuthorizedKeys
{
    /** @var GetLoggedInServerSshConnection */
    private $getConnection;

    public function __construct(GetLoggedInServerSshConnection $getConnection)
    {
        $this->getConnection = $getConnection;
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
        $ssh = $this->getConnection->get($server);

        $keysString = $ssh->exec('cat ~/.ssh/authorized_keys');

        $keysString = trim($keysString);

        return explode("\n", $keysString);
    }
}
