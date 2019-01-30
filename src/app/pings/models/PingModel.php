<?php
declare(strict_types=1);

namespace src\app\pings\models;

use DateTime;
use DateTimeZone;

use src\app\support\traits\ModelErrorsTrait;
use src\app\support\traits\ModelAddedAtTrait;
use src\app\support\traits\StandardModelTrait;
use src\app\pings\interfaces\PingModelInterface;

class PingModel implements PingModelInterface
{
    use ModelErrorsTrait;
    use ModelAddedAtTrait;
    use StandardModelTrait;

    private $pingId = '';

    public function pingId(?string $val = null): string
    {
        return $this->pingId = $val ?? $this->pingId;
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

    /** @var DateTime|null */
    private $lastPingAt;

    public function lastPingAt(?DateTime $val = null): DateTime
    {
        if (! $val && ! $this->addedAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->lastPingAt = new DateTime('now', new DateTimeZone('UTC'));
        }

        return $this->lastPingAt = $val ?? $this->lastPingAt;
    }

    private $lastNotificationAt;

    public function lastNotificationAt(?DateTime $val = null): ?DateTime
    {
        return $this->lastNotificationAt = $val ?? $this->lastNotificationAt;
    }

    public function clearLastNotificationAt()
    {
        $this->lastNotificationAt = null;
    }
}
