<?php

declare(strict_types=1);

namespace src\app\pings\events;

use corbomite\events\interfaces\EventInterface;
use src\app\pings\interfaces\PingModelInterface;
use src\app\pings\PingApi;

class PingBeforeSaveEvent implements EventInterface
{
    /** @var bool */
    private $isNew;
    /** @var PingModelInterface */
    private $pingModel;

    public function __construct(
        PingModelInterface $pingModel,
        bool $isNew = false
    ) {
        $this->isNew     = $isNew;
        $this->pingModel = $pingModel;
    }

    public function isNew() : bool
    {
        return $this->isNew;
    }

    public function pingModel() : PingModelInterface
    {
        return $this->pingModel;
    }

    public function provider() : string
    {
        return PingApi::class;
    }

    public function name() : string
    {
        return 'PingBeforeSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
