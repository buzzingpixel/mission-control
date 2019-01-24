<?php
declare(strict_types=1);

namespace src\app\reminders\events;

use src\app\reminders\ReminderApi;
use corbomite\events\interfaces\EventInterface;
use src\app\reminders\interfaces\ReminderModelInterface;

class ReminderAfterUnArchiveEvent implements EventInterface
{
    private $reminderModel;

    public function __construct(ReminderModelInterface $reminderModel)
    {
        $this->reminderModel = $reminderModel;
    }

    public function pingModel(): ReminderModelInterface
    {
        return $this->reminderModel;
    }

    public function provider(): string
    {
        return ReminderApi::class;
    }

    public function name(): string
    {
        return 'ReminderAfterUnArchive';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
