<?php
declare(strict_types=1);

namespace src\app\datasupport;

class FetchDataParamsFactory
{
    public function __invoke(): FetchDataParamsInterface
    {
        return $this->make();
    }

    public function make(): FetchDataParamsInterface
    {
        return new FetchDataParams();
    }
}
