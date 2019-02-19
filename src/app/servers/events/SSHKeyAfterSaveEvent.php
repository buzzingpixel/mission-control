<?php
declare(strict_types=1);

namespace src\app\servers\events;

use src\app\servers\ServerApi;
use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;

class SSHKeyAfterSaveEvent implements EventInterface
{
    private $wasNew;
    private $sshKeyModel;

    public function __construct(
        SSHKeyModelInterface $sshKeyModel,
        bool $wasNew = false
    ) {
        $this->wasNew = $wasNew;
        $this->sshKeyModel = $sshKeyModel;
    }

    public function wasNew(): bool
    {
        return $this->wasNew;
    }

    public function sshKeyModel(): SSHKeyModelInterface
    {
        return $this->sshKeyModel;
    }

    public function provider(): string
    {
        return ServerApi::class;
    }

    public function name(): string
    {
        return 'SSHKeyAfterSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
