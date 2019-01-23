<?php
declare(strict_types=1);

namespace src\app\support\interfaces;

use corbomite\db\interfaces\UuidModelInterface;

interface HasProjectGuidInterface extends HasGuidInterface
{
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
}
