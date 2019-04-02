<?php

declare(strict_types=1);

namespace src\app\monitoredurls\models;

use corbomite\db\interfaces\UuidModelInterface;
use corbomite\db\models\UuidModel;
use corbomite\db\traits\UuidTrait;
use DateTime;
use DateTimeZone;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;

class MonitoredUrlIncidentModel implements MonitoredUrlIncidentModelInterface
{
    use UuidTrait;

    /** @var UuidModelInterface */
    private $monitoredUrlUuidModel;

    public function monitoredUrlGuid(?string $guid = null) : ?string
    {
        if ($guid !== null) {
            $this->monitoredUrlUuidModel = new UuidModel($guid);
        }

        if (! $this->monitoredUrlUuidModel) {
            return null;
        }

        return $this->monitoredUrlUuidModel->toString();
    }

    public function monitoredUrlGuidAsModel() : ?UuidModelInterface
    {
        return $this->monitoredUrlUuidModel;
    }

    public function getMonitoredUrlGuidAsBytes() : ?string
    {
        if (! $this->monitoredUrlUuidModel) {
            return null;
        }

        return $this->monitoredUrlUuidModel->toBytes();
    }

    public function setMonitoredUrlGuidAsBytes(string $bytes) : void
    {
        $this->monitoredUrlUuidModel = UuidModel::fromBytes($bytes);
    }

    /** @var string */
    private $eventType = '';

    public function eventType(?string $val = null) : string
    {
        return $this->eventType = $val ?? $this->eventType;
    }

    /** @var string */
    private $statusCode = '';

    public function statusCode(?string $val = null) : string
    {
        return $this->statusCode = $val ?? $this->statusCode;
    }

    /** @var string */
    private $message = '';

    public function message(?string $val = null) : string
    {
        return $this->message = $val ?? $this->message;
    }

    /** @var DateTime|null */
    private $eventAt;

    public function eventAt(?DateTime $val = null) : DateTime
    {
        if (! $val && ! $this->eventAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->eventAt = new DateTime('now', new DateTimeZone('UTC'));
        }

        return $this->eventAt = $val ?? $this->eventAt;
    }

    /** @var DateTime|null */
    private $lastNotificationAt;

    public function lastNotificationAt(?DateTime $val = null) : ?DateTime
    {
        return $this->lastNotificationAt = $val ?? $this->lastNotificationAt;
    }
}
