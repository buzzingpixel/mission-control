<?php
declare(strict_types=1);

namespace src\app\reminders\models;

use src\app\support\traits\ModelAddedAtTrait;
use src\app\support\traits\StandardModelTrait;
use src\app\reminders\interfaces\ReminderModelInterface;

class ReminderModel implements ReminderModelInterface
{
    use ModelAddedAtTrait;
    use StandardModelTrait;

    private $message = '';

    public function message(?string $val = null): string
    {
        return $this->message = $val ?? $this->message;
    }
}
