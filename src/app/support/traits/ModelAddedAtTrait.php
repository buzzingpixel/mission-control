<?php
declare(strict_types=1);

namespace src\app\support\traits;

use DateTime;
use DateTimeZone;

trait ModelAddedAtTrait
{
    /** @var DateTime */
    private $addedAt;

    public function addedAt(?DateTime $val = null): DateTime
    {
        if (! $val && ! $this->addedAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addedAt = new DateTime('now', new DateTimeZone('UTC'));
        }

        return $this->addedAt = $val ?? $this->addedAt;
    }
}
