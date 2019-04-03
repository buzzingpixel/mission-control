<?php

declare(strict_types=1);

namespace src\app\servers\events;

use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\ServerApi;

class ServerBeforeSaveEvent implements EventInterface
{
    /** @var bool */
    private $isNew;
    /** @var ServerModelInterface */
    private $serverModel;

    public function __construct(
        ServerModelInterface $serverModel,
        bool $isNew = false
    ) {
        $this->isNew       = $isNew;
        $this->serverModel = $serverModel;
    }

    public function isNew() : bool
    {
        return $this->isNew;
    }

    public function serverModel() : ServerModelInterface
    {
        return $this->serverModel;
    }

    public function provider() : string
    {
        return ServerApi::class;
    }

    public function name() : string
    {
        return 'ServerBeforeSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
