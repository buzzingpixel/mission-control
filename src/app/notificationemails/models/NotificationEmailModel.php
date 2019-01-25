<?php
declare(strict_types=1);

namespace src\app\notificationemails\models;

use corbomite\db\traits\UuidTrait;
use src\app\notificationemails\interfaces\NotificationEmailModelInterface;

class NotificationEmailModel implements NotificationEmailModelInterface
{
    use UuidTrait;

    private $isActive = true;

    public function isActive(?bool $val = null): bool
    {
        return $this->isActive = $val ?? $this->isActive;
    }

    private $emailAddress = '';

    public function emailAddress(?string $val = null): string
    {
        return $this->emailAddress = $val ?? $this->emailAddress;
    }
}
