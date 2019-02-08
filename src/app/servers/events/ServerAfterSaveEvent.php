<?php
declare(strict_types=1);

namespace src\app\servers\events;

use src\app\servers\ServerApi;
use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\ServerModelInterface;

class ServerAfterSaveEvent implements EventInterface
{
    private $wasNew;
    private $serverModel;

    public function __construct(
        ServerModelInterface $serverModel,
        bool $wasNew = false
    ) {
        $this->wasNew = $wasNew;
        $this->serverModel = $serverModel;
    }

    public function wasNew(): bool
    {
        return $this->wasNew;
    }

    public function pingModel(): ServerModelInterface
    {
        return $this->serverModel;
    }

    public function provider(): string
    {
        return ServerApi::class;
    }

    public function name(): string
    {
        return 'ServerAfterSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
