<?php

declare(strict_types=1);

namespace src\app\reminders\exceptions;

use Exception;
use Throwable;

class InvalidReminderModelException extends Exception
{
    public function __construct(
        string $message = 'The Reminder model is not valid',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
