<?php

declare(strict_types=1);

namespace src\app\servers\events;

use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\ServerApi;

class SSHKeyAfterSaveEvent implements EventInterface
{
    /** @var bool */
    private $wasNew;
    /** @var SSHKeyModelInterface */
    private $sshKeyModel;

    public function __construct(
        SSHKeyModelInterface $sshKeyModel,
        bool $wasNew = false
    ) {
        $this->wasNew      = $wasNew;
        $this->sshKeyModel = $sshKeyModel;
    }

    public function wasNew() : bool
    {
        return $this->wasNew;
    }

    public function sshKeyModel() : SSHKeyModelInterface
    {
        return $this->sshKeyModel;
    }

    public function provider() : string
    {
        return ServerApi::class;
    }

    public function name() : string
    {
        return 'SSHKeyAfterSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
