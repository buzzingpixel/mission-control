<?php

declare(strict_types=1);

namespace src\app\support\traits;

trait StandardModelStandaloneTrait
{
    /** @var bool */
    private $isActive = true;

    public function isActive(?bool $val = null) : bool
    {
        return $this->isActive = $val ?? $this->isActive;
    }

    /** @var string */
    private $title = '';

    public function title(?string $val = null) : string
    {
        return $this->title = $val ?? $this->title;
    }

    /** @var string */
    private $slug = '';

    public function slug(?string $val = null) : string
    {
        return $this->slug = $val ?? $this->slug;
    }
}
