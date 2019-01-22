<?php
declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use DateTime;
use corbomite\db\interfaces\UuidModelInterface;

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
     * Gets the UuidModel for the guid
     * @return UuidModelInterface
     */
    public function guidAsModel(): UuidModelInterface;

    /**
     * Gets the GUID as bytes for saving to the database in binary
     * @return string
     */
    public function getGuidAsBytes(): string;

    /**
     * Sets the GUID from bytes coming from the database binary column
     * @param string $bytes
     */
    public function setGuidAsBytes(string $bytes);

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param string|null $val
     * @return string|null
     */
    public function projectGuid(?string $val = null): ?string;

    /**
     * Gets the UuidModel for the project GUID
     * @return UuidModelInterface
     */
    public function projectGuidAsModel(): ?UuidModelInterface;

    /**
     * Gets the project GUID as bytes for saving to the database in binary
     * @return string
     */
    public function getProjectGuidAsBytes(): ?string;

    /**
     * Sets the project GUID from bytes coming from the database binary column
     * @param string $bytes
     */
    public function setProjectGuidAsBytes(string $bytes);

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
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function hasError(?bool $val = null): bool;

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
