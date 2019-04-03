<?php

declare(strict_types=1);

namespace src\app\servers\events;

use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\ServerApi;

class SSHKeyAfterDeleteEvent implements EventInterface
{
    /** @var SSHKeyModelInterface */
    private $model;

    public function __construct(SSHKeyModelInterface $model)
    {
        $this->model = $model;
    }

    public function model() : SSHKeyModelInterface
    {
        return $this->model;
    }

    public function provider() : string
    {
        return ServerApi::class;
    }

    public function name() : string
    {
        return 'SSHKeyAfterDelete';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
