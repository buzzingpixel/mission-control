<?php
declare(strict_types=1);

namespace src\app\pings\events;

use src\app\pings\PingApi;
use corbomite\events\interfaces\EventInterface;
use src\app\pings\interfaces\PingModelInterface;

class PingBeforeArchiveEvent implements EventInterface
{
    private $pingModel;

    public function __construct(PingModelInterface $pingModel)
    {
        $this->pingModel = $pingModel;
    }

    public function pingModel(): PingModelInterface
    {
        return $this->pingModel;
    }

    public function provider(): string
    {
        return PingApi::class;
    }

    public function name(): string
    {
        return 'PingBeforeArchive';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
