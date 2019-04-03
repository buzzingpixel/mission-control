<?php

declare(strict_types=1);

namespace src\app\support\traits;

trait ModelErrorsTrait
{
    /** @var bool */
    private $pendingError = false;

    public function pendingError(?bool $val = null) : bool
    {
        return $this->pendingError = $val ?? $this->pendingError;
    }

    /** @var bool */
    private $hasError = false;

    public function hasError(?bool $val = null) : bool
    {
        return $this->hasError = $val ?? $this->hasError;
    }
}
