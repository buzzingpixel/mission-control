<?php

declare(strict_types=1);

namespace src\app\utilities;

use phpseclib\Net\SSH2;

class SSH2Factory
{
    public function __invoke(string $host, int $port = 22, int $timeout = 10) : SSH2
    {
        return $this->make($host, $port, $timeout);
    }

    public function make(string $host, int $port = 22, int $timeout = 10) : SSH2
    {
        return new SSH2($host, $port, $timeout);
    }
}
