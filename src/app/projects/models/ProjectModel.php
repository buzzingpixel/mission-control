<?php
declare(strict_types=1);

namespace src\app\projects\models;

use DateTime;
use DateTimeZone;

use src\app\projects\interfaces\ProjectModelInterface;

class ProjectModel implements ProjectModelInterface
{
    public function __construct(array $props = [])
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->addedAt = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($props as $k => $v) {
            $this->{$k}($v);
        }
    }

    private $guid = '';

    public function guid(?string $guid = null): string
    {
        return $this->guid = $guid ?? $this->guid;
    }

    private $isActive = true;

    public function isActive(?bool $isActive = null): bool
    {
        return $this->isActive = $isActive ?? $this->isActive;
    }

    private $title = '';

    public function title(?string $title = null): string
    {
        return $this->title = $title ?? $this->title;
    }

    private $slug = '';

    public function slug(?string $slug = null): string
    {
        return $this->slug = $slug ?? $this->slug;
    }

    private $description = '';

    public function description(?string $description = null): string
    {
        return $this->description = $description ?? $this->description;
    }

    private $addedAt;

    public function addedAt(?DateTime $addedAt = null): DateTime
    {
        return $this->addedAt = $addedAt ?? $this->addedAt;
    }
}
