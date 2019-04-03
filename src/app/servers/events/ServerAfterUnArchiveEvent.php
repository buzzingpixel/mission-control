<?php

declare(strict_types=1);

namespace src\app\servers\events;

use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\ServerApi;

class ServerAfterUnArchiveEvent implements EventInterface
{
    /** @var ServerModelInterface */
    private $model;

    public function __construct(ServerModelInterface $model)
    {
        $this->model = $model;
    }

    public function model() : ServerModelInterface
    {
        return $this->model;
    }

    public function provider() : string
    {
        return ServerApi::class;
    }

    public function name() : string
    {
        return 'ServerAfterUnArchive';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
