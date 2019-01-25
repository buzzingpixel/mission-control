<?php
declare(strict_types=1);

namespace src\app\notificationemails\interfaces;

use src\app\support\interfaces\HasGuidInterface;

interface NotificationEmailModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function isActive(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function emailAddress(?string $val = null): string;
}
