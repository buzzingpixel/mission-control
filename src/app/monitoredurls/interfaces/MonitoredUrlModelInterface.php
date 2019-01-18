<?php
declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use DateTime;

interface MonitoredUrlModelInterface
{
    /**
     * Constructor accepts array of properties to set on the model
     * @param array $props
     */
    public function __construct(array $props = []);

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $guid
     * @return string
     */
    public function guid(?string $val = null): string;

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
    public function url(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function pendingError(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     * @param DateTime|null $val
     * @return DateTime
     */
    public function checkedAt(?DateTime $val = null): DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     * @param DateTime|null $val
     * @return DateTime
     */
    public function addedAt(?DateTime $val = null): DateTime;
}
