<?php

declare(strict_types=1);

namespace src\app\servers\services;

use src\app\utilities\RSAFactory;

class GenerateSSHKeyService
{
    /** @var RSAFactory */
    private $rsaFactory;

    public function __construct(RSAFactory $rsaFactory)
    {
        $this->rsaFactory = $rsaFactory;
    }

    public function __invoke() : void
    {
        $this->generate();
    }

    /**
     * @return string[]
     */
    public function generate() : array
    {
        return $this->rsaFactory->make()->createKey(2048);
    }
}
