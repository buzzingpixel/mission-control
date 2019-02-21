<?php
declare(strict_types=1);

namespace src\app\servers\events;

use src\app\servers\ServerApi;
use corbomite\events\interfaces\EventInterface;
use src\app\servers\interfaces\SSHKeyModelInterface;

class SSHKeyAfterUnArchiveEvent implements EventInterface
{
    private $model;

    public function __construct(SSHKeyModelInterface $model)
    {
        $this->model = $model;
    }

    public function model(): SSHKeyModelInterface
    {
        return $this->model;
    }

    public function provider(): string
    {
        return ServerApi::class;
    }

    public function name(): string
    {
        return 'SSHKeyAfterUnArchive';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
