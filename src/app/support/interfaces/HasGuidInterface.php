<?php

declare(strict_types=1);

namespace src\app\support\interfaces;

use corbomite\db\interfaces\UuidModelInterface;

interface HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     *
     * @param string|null $guid
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
     *
     * @return mixed
     */
    public function setGuidAsBytes(string $bytes);
}
