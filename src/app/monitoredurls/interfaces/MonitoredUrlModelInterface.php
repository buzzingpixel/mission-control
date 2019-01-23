<?php
declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use DateTime;
use src\app\support\interfaces\StandardModelInterface;

interface MonitoredUrlModelInterface extends StandardModelInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function url(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function pendingError(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function hasError(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime
     * @param DateTime|null $val
     * @return DateTime
     */
    public function checkedAt(?DateTime $val = null): DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime
     * @param DateTime|null $val
     * @return DateTime
     */
    public function addedAt(?DateTime $val = null): DateTime;
}
