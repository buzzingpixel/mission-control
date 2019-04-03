<?php

declare(strict_types=1);

namespace src\app\reminders\models;

use DateTime;
use src\app\reminders\interfaces\ReminderModelInterface;
use src\app\support\traits\ModelAddedAtTrait;
use src\app\support\traits\StandardModelTrait;

class ReminderModel implements ReminderModelInterface
{
    use ModelAddedAtTrait;
    use StandardModelTrait;

    /** @var string */
    private $message = '';

    public function message(?string $val = null) : string
    {
        return $this->message = $val ?? $this->message;
    }

    /** @var DateTime|null */
    private $startRemindingOn;

    public function startRemindingOn(?DateTime $val = null) : DateTime
    {
        return $this->startRemindingOn = $val ?? $this->startRemindingOn;
    }

    /** @var DateTime|null */
    private $lastReminderSent;

    public function lastReminderSent(?DateTime $val = null) : ?DateTime
    {
        return $this->lastReminderSent = $val ?? $this->lastReminderSent;
    }
}
