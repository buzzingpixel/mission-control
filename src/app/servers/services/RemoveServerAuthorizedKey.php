<?php

declare(strict_types=1);

namespace src\app\servers\services;

use src\app\servers\interfaces\ServerModelInterface;

class RemoveServerAuthorizedKey
{
    /** @var GetLoggedInServerSshConnection */
    private $getConnection;

    public function __construct(GetLoggedInServerSshConnection $getConnection)
    {
        $this->getConnection = $getConnection;
    }

    public function __invoke(string $key, ServerModelInterface $server) : void
    {
        $this->remove($key, $server);
    }

    public function remove(string $key, ServerModelInterface $server) : void
    {
        $ssh = $this->getConnection->get($server);

        $ssh->exec(
            'if test -f ~/.ssh/authorized_keys; then ' .
                'if grep -v "' . $key . '" ~/.ssh/authorized_keys > $HOME/.ssh/tmp; then ' .
                    'cat $HOME/.ssh/tmp > ~/.ssh/authorized_keys && rm $HOME/.ssh/tmp; ' .
                'fi ' .
            'fi'
        );
    }
}
