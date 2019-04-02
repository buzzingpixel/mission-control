<?php

declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use corbomite\db\interfaces\UuidModelInterface;
use DateTime;

interface MonitoredUrlIncidentModelInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function guid(?string $val = null) : string;

    /**
     * Gets the UuidModel for the guid
     */
    public function guidAsModel() : UuidModelInterface;

    /**
     * Gets the GUID as bytes for saving to the database in binary
     */
    public function getGuidAsBytes() : string;

    /**
     * Sets the GUID from bytes coming from the database binary column
     */
    public function setGuidAsBytes(string $bytes) : void;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function monitoredUrlGuid(?string $val = null) : ?string;

    /**
     * Gets the UuidModel for the monitored URL GUID
     */
    public function monitoredUrlGuidAsModel() : ?UuidModelInterface;

    /**
     * Gets the monitored URL GUID as bytes for saving to the database in binary
     */
    public function getMonitoredUrlGuidAsBytes() : ?string;

    /**
     * Sets the monitored URL GUID from bytes coming from the database binary column
     */
    public function setMonitoredUrlGuidAsBytes(string $bytes) : void;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function eventType(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function statusCode(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function message(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     */
    public function eventAt(?DateTime $val = null) : DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function lastNotificationAt(?DateTime $val = null) : ?DateTime;
}
