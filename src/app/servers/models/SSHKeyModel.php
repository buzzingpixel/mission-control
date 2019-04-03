<?php

declare(strict_types=1);

namespace src\app\servers\models;

use corbomite\db\traits\UuidTrait;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\support\traits\StandardModelStandaloneTrait;

class SSHKeyModel implements SSHKeyModelInterface
{
    use UuidTrait;
    use StandardModelStandaloneTrait;

    /** @var ?string */
    private $public;

    public function public(?string $val = null) : ?string
    {
        return $this->public = $val ?? $this->public;
    }

    /** @var ?string */
    private $private;

    public function private(?string $val = null) : ?string
    {
        return $this->private = $val ?? $this->private;
    }
}
