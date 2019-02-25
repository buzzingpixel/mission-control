<?php
declare(strict_types=1);

namespace src\app\servers\services;

use phpseclib\Crypt\RSA;

class GenerateSSHKeyService
{
    private $rsa;

    public function __construct(RSA $rsa)
    {
        $this->rsa = $rsa;
    }

    public function __invoke(): void
    {
        $this->generate();
    }

    public function generate(): array
    {
        return $this->rsa->createKey(2048);
    }
}
