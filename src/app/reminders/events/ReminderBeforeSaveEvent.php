<?php

declare(strict_types=1);

namespace src\app\reminders\events;

use corbomite\events\interfaces\EventInterface;
use src\app\reminders\interfaces\ReminderModelInterface;
use src\app\reminders\ReminderApi;

class ReminderBeforeSaveEvent implements EventInterface
{
    /** @var bool */
    private $isNew;
    /** @var ReminderModelInterface */
    private $reminderModel;

    public function __construct(
        ReminderModelInterface $reminderModel,
        bool $isNew = false
    ) {
        $this->isNew         = $isNew;
        $this->reminderModel = $reminderModel;
    }

    public function isNew() : bool
    {
        return $this->isNew;
    }

    public function pingModel() : ReminderModelInterface
    {
        return $this->reminderModel;
    }

    public function provider() : string
    {
        return ReminderApi::class;
    }

    public function name() : string
    {
        return 'ReminderBeforeSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
