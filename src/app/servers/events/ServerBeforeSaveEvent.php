<?php
declare(strict_types=1);

namespace src\app\servers\events;

use src\app\servers\ServerApi;
use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\ServerModelInterface;

class ServerBeforeSaveEvent implements EventInterface
{
    private $isNew;
    private $serverModel;

    public function __construct(
        ServerModelInterface $serverModel,
        bool $isNew = false
    ) {
        $this->isNew = $isNew;
        $this->serverModel = $serverModel;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function serverModel(): ServerModelInterface
    {
        return $this->serverModel;
    }

    public function provider(): string
    {
        return ServerApi::class;
    }

    public function name(): string
    {
        return 'ServerBeforeSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
