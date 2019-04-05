<?php

declare(strict_types=1);

namespace src\app\utilities;

use phpseclib\Crypt\RSA;

class RSAFactory
{
    public function __invoke(?string $privateKey = null, ?string $publicKey = null) : RSA
    {
        return $this->make($privateKey, $publicKey);
    }

    public function make(?string $privateKey = null, ?string $publicKey = null) : RSA
    {
        $rsa = new RSA();

        $rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);

        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);

        if ($publicKey !== null) {
            $rsa->loadKey($publicKey, RSA::PUBLIC_FORMAT_OPENSSH);
        }

        if ($privateKey !== null) {
            $rsa->loadKey($privateKey, RSA::PRIVATE_FORMAT_PKCS1);
        }

        return $rsa;
    }
}
