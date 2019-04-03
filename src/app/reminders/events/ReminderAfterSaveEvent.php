<?php

declare(strict_types=1);

namespace src\app\reminders\events;

use corbomite\events\interfaces\EventInterface;
use src\app\reminders\interfaces\ReminderModelInterface;
use src\app\reminders\ReminderApi;

class ReminderAfterSaveEvent implements EventInterface
{
    /** @var bool */
    private $wasNew;
    /** @var ReminderModelInterface */
    private $reminderModel;

    public function __construct(
        ReminderModelInterface $reminderModel,
        bool $wasNew = false
    ) {
        $this->wasNew        = $wasNew;
        $this->reminderModel = $reminderModel;
    }

    public function wasNew() : bool
    {
        return $this->wasNew;
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
        return 'ReminderAfterSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
