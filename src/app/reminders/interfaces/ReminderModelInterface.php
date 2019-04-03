<?php

declare(strict_types=1);

namespace src\app\reminders\interfaces;

use DateTime;
use src\app\support\interfaces\StandardModelInterface;

interface ReminderModelInterface extends StandardModelInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function message(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function startRemindingOn(?DateTime $val = null) : DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function lastReminderSent(?DateTime $val = null) : ?DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     */
    public function addedAt(?DateTime $val = null) : DateTime;
}
