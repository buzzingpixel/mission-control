<?php
declare(strict_types=1);

namespace src\app\monitoredurls\models;

use DateTime;
use DateTimeZone;
use corbomite\db\traits\UuidTrait;
use corbomite\db\models\UuidModel;
use corbomite\db\interfaces\UuidModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class MonitoredUrlModel implements MonitoredUrlModelInterface
{
    use UuidTrait;

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

    /** @var UuidModelInterface */
    private $projectUuidModel;

    public function projectGuid(?string $guid = null): ?string
    {
        if ($guid !== null) {
            $this->projectUuidModel = new UuidModel($guid);
        }

        if (! $this->projectUuidModel) {
            return null;
        }

        return $this->projectUuidModel->toString();
    }

    public function projectGuidAsModel(): ?UuidModelInterface
    {
        return $this->projectUuidModel;
    }

    public function getProjectGuidAsBytes(): ?string
    {
        if (! $this->projectUuidModel) {
            return null;
        }

        return $this->projectUuidModel->toBytes();
    }

    public function setProjectGuidAsBytes(string $bytes): void
    {
        $this->projectUuidModel = UuidModel::fromBytes($bytes);
    }

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

    private $url = '';

    public function url(?string $val = null): string
    {
        return $this->url = $val ?? $this->url;
    }

    private $pendingError = false;

    public function pendingError(?bool $val = null): bool
    {
        return $this->pendingError = $val ?? $this->pendingError;
    }

    private $hasError = false;

    public function hasError(?bool $val = null): bool
    {
        return $this->hasError = $val ?? $this->hasError;
    }

    private $checkedAt;

    public function checkedAt(?DateTime $val = null): DateTime
    {
        return $this->checkedAt = $val ?? $this->checkedAt;
    }

    private $addedAt;

    public function addedAt(?DateTime $val = null): DateTime
    {
        return $this->addedAt = $val ?? $this->addedAt;
    }
}
