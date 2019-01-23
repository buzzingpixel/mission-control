<?php
declare(strict_types=1);

namespace src\app\support\traits;

trait StandardModelTrait
{
    use HasProjectGuidTrait;

    private $isActive = true;

    public function isActive(?bool $val = null): bool
    {
        return $this->isActive = $val ?? $this->isActive;
    }

    private $title = '';

    public function title(?string $val = null): string
    {
        return $this->title = $val ?? $this->title;
    }

    private $slug = '';

    public function slug(?string $val = null): string
    {
        return $this->slug = $val ?? $this->slug;
    }
}
