<?php

declare(strict_types=1);

namespace src\app\notificationemails\exceptions;

use Exception;
use Throwable;

class InvalidNotificationEmailModelException extends Exception
{
    public function __construct(
        string $message = 'The Notification Email model is not valid',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
