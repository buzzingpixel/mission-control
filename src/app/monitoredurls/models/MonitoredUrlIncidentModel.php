<?php
declare(strict_types=1);

namespace src\app\monitoredurls\models;

use DateTime;
use DateTimeZone;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;

class MonitoredUrlIncidentModel implements MonitoredUrlIncidentModelInterface
{
    public function __construct(array $props = [])
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->eventAt = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($props as $k => $v) {
            $this->{$k}($v);
        }
    }

    private $guid = '';

    public function guid(?string $val = null): string
    {
        return $this->guid = $val ?? $this->guid;
    }

    private $monitoredUrlGuid = '';

    public function monitoredUrlGuid(?string $val = null): string
    {
        return $this->monitoredUrlGuid = $val ?? $this->monitoredUrlGuid;
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
