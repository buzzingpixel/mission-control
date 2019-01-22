<?php
declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use DateTime;
use corbomite\db\interfaces\UuidModelInterface;

interface MonitoredUrlIncidentModelInterface
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
    public function monitoredUrlGuid(?string $val = null): ?string;

    /**
     * Gets the UuidModel for the monitored URL GUID
     * @return UuidModelInterface
     */
    public function monitoredUrlGuidAsModel(): ?UuidModelInterface;

    /**
     * Gets the monitored URL GUID as bytes for saving to the database in binary
     * @return string
     */
    public function getMonitoredUrlGuidAsBytes(): ?string;

    /**
     * Sets the monitored URL GUID from bytes coming from the database binary column
     * @param string $bytes
     */
    public function setMonitoredUrlGuidAsBytes(string $bytes);

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param string|null $val
     * @return string
     */
    public function eventType(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param string|null $val
     * @return string
     */
    public function statusCode(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param string|null $val
     * @return string
     */
    public function message(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     * @param DateTime|null $val
     * @return DateTime
     */
    public function eventAt(?DateTime $val = null): DateTime;
}
