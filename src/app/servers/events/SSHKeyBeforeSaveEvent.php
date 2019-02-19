<?php
declare(strict_types=1);

namespace src\app\servers\events;

use src\app\servers\ServerApi;
use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;

class SSHKeyBeforeSaveEvent implements EventInterface
{
    private $isNew;
    private $sshKeyModel;

    public function __construct(
        SSHKeyModelInterface $sshKeyModel,
        bool $isNew = false
    ) {
        $this->isNew = $isNew;
        $this->sshKeyModel = $sshKeyModel;
    }

    public function isNew(): bool
    {
        return $this->isNew;
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
        return 'SSHKeyBeforeSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
