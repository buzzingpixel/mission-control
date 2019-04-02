<?php

declare(strict_types=1);

namespace src\app\notificationemails\interfaces;

use src\app\support\interfaces\HasGuidInterface;

interface NotificationEmailModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function isActive(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function emailAddress(?string $val = null) : string;
}
