<?php
declare(strict_types=1);

namespace src\app\pings\interfaces;

use DateTime;
use src\app\support\interfaces\StandardModelInterface;

interface PingModelInterface extends StandardModelInterface
{
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
     * Returns the value. Sets value if incoming argument is set
     * @param int|null $val
     * @return int|null
     */
    public function expectEvery(?int $val = null): ?int;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param int|null $val
     * @return int|null
     */
    public function warnAfter(?int $val = null): ?int;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     * @param DateTime|null $val
     * @return DateTime
     */
    public function lastPingAt(?DateTime $val = null): DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     * @param DateTime|null $val
     * @return DateTime
     */
    public function addedAt(?DateTime $val = null): DateTime;
}
