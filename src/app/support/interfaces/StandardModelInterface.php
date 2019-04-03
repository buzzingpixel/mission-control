<?php

declare(strict_types=1);

namespace src\app\support\interfaces;

interface StandardModelInterface extends HasProjectGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function isActive(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function title(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function slug(?string $val = null) : string;
}
