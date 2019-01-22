<?php
declare(strict_types=1);

namespace src\app\monitoredurls\models;

use DateTime;
use DateTimeZone;
use corbomite\db\traits\UuidTrait;
use corbomite\db\models\UuidModel;
use corbomite\db\interfaces\UuidModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;

class MonitoredUrlIncidentModel implements MonitoredUrlIncidentModelInterface
{
    use UuidTrait;

    public function __construct(array $props = [])
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->eventAt = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($props as $k => $v) {
            $this->{$k}($v);
        }
    }

    /** @var UuidModelInterface */
    private $monitoredUrlUuidModel;

    public function monitoredUrlGuid(?string $guid = null): ?string
    {
        if ($guid !== null) {
            $this->monitoredUrlUuidModel = new UuidModel($guid);
        }

        if (! $this->monitoredUrlUuidModel) {
            return null;
        }

        return $this->monitoredUrlUuidModel->toString();
    }

    public function monitoredUrlGuidAsModel(): ?UuidModelInterface
    {
        return $this->monitoredUrlUuidModel;
    }

    public function getMonitoredUrlGuidAsBytes(): ?string
    {
        if (! $this->monitoredUrlUuidModel) {
            return null;
        }

        return $this->monitoredUrlUuidModel->toBytes();
    }

    public function setMonitoredUrlGuidAsBytes(string $bytes): void
    {
        $this->monitoredUrlUuidModel = UuidModel::fromBytes($bytes);
    }

    private $eventType = '';

    public function eventType(?string $val = null): string
    {
        return $this->eventType = $val ?? $this->eventType;
    }

    private $statusCode = '';

    public function statusCode(?string $val = null): string
    {
        return $this->statusCode = $val ?? $this->statusCode;
    }

    private $message = '';

    public function message(?string $val = null): string
    {
        return $this->message = $val ?? $this->message;
    }

    private $eventAt;

    public function eventAt(?DateTime $val = null): DateTime
    {
        return $this->eventAt = $val ?? $this->eventAt;
    }
}
