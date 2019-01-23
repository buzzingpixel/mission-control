<?php
declare(strict_types=1);

namespace src\app\pings\models;

use DateTime;
use DateTimeZone;

use src\app\support\traits\StandardModelTrait;
use src\app\pings\interfaces\PingModelInterface;

class PingModel implements PingModelInterface
{
    use StandardModelTrait;

    public function __construct()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->addedAt = new DateTime('now', new DateTimeZone('UTC'));

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->lastPingAt = new DateTime('now', new DateTimeZone('UTC'));
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

    /** @var int|null */
    private $expectEvery;

    public function expectEvery(?int $val = null): ?int
    {
        return $this->expectEvery = $val ?? $this->expectEvery;
    }

    /** @var int|null */
    private $warnAfter;

    public function warnAfter(?int $val = null): ?int
    {
        return $this->warnAfter = $val ?? $this->warnAfter;
    }

    private $lastPingAt;

    public function lastPingAt(?DateTime $val = null): DateTime
    {
        return $this->lastPingAt = $val ?? $this->lastPingAt;
    }

    private $addedAt;

    public function addedAt(?DateTime $val = null): DateTime
    {
        return $this->addedAt = $val ?? $this->addedAt;
    }
}
