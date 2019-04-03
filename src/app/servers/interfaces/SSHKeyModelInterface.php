<?php

declare(strict_types=1);

namespace src\app\servers\interfaces;

use src\app\support\interfaces\HasGuidInterface;

interface SSHKeyModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function isActive(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function title(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function slug(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function public(?string $val = null) : ?string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function private(?string $val = null) : ?string;
}
