<?php
declare(strict_types=1);

namespace src\app\servers\interfaces;

use src\app\support\interfaces\HasGuidInterface;

interface SSHKeyModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function isActive(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function title(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function slug(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function public(?string $val = null): ?string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function private(?string $val = null): ?string;
}
