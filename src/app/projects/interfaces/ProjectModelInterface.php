<?php

declare(strict_types=1);

namespace src\app\projects\interfaces;

use corbomite\db\interfaces\UuidModelInterface;
use DateTime;

interface ProjectModelInterface
{
    /**
     * Returns the value of guid. Sets value if incoming argument is set
     */
    public function guid(?string $guid = null) : string;

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
     *
     * @return mixed
     */
    public function setGuidAsBytes(string $bytes);

    /**
     * Returns the value of isActive. Sets value if incoming argument is set
     */
    public function isActive(?bool $isActive = null) : bool;

    /**
     * Returns the value of title. Sets value if incoming argument is set
     */
    public function title(?string $title = null) : string;

    /**
     * Returns the value of slug. Sets value if incoming argument is set
     */
    public function slug(?string $slug = null) : string;

    /**
     * Returns the value of description. Sets value if incoming argument is set
     */
    public function description(?string $description = null) : string;

    /**
     * Returns the value of addedAt. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     */
    public function addedAt(?DateTime $addedAt = null) : DateTime;
}
