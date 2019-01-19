<?php
declare(strict_types=1);

namespace src\app\monitoredurls\models;

use DateTime;
use DateTimeZone;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class MonitoredUrlModel implements MonitoredUrlModelInterface
{
    public function __construct(array $props = [])
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->addedAt = new DateTime('now', new DateTimeZone('UTC'));

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->checkedAt = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($props as $k => $v) {
            $this->{$k}($v);
        }
    }

    private $guid = '';

    public function guid(?string $val = null): string
    {
        return $this->guid = $val !== null ? $val : $this->guid;
    }

    private $projectGuid;

    public function projectGuid(?string $val = null): string
    {
        return $this->projectGuid = $val !== null ? $val : $this->projectGuid;
    }

    private $isActive = true;

    public function isActive(?bool $val = null): bool
    {
        return $this->isActive = $val !== null ? $val : $this->isActive;
    }

    private $title = '';

    public function title(?string $val = null): string
    {
        return $this->title = $val !== null ? $val : $this->title;
    }

    private $slug = '';

    public function slug(?string $val = null): string
    {
        return $this->slug = $val !== null ? $val : $this->slug;
    }

    private $url = '';

    public function url(?string $val = null): string
    {
        return $this->url = $val !== null ? $val : $this->url;
    }

    private $pendingError = false;

    public function pendingError(?bool $val = null): bool
    {
        return $this->pendingError = $val !== null ? $val : $this->pendingError;
    }

    private $hasError = false;

    public function hasError(?bool $val = null): bool
    {
        return $this->hasError = $val !== null ? $val : $this->hasError;
    }

    private $checkedAt;

    public function checkedAt(?DateTime $val = null): DateTime
    {
        return $this->checkedAt = $val !== null ? $val : $this->checkedAt;
    }

    private $addedAt;

    public function addedAt(?DateTime $val = null): DateTime
    {
        return $this->addedAt = $val !== null ? $val : $this->addedAt;
    }
}
