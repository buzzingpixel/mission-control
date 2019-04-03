<?php

declare(strict_types=1);

namespace src\app\pings\exceptions;

use Exception;
use Throwable;

class InvalidPingModelException extends Exception
{
    public function __construct(
        string $message = 'The ping model is not valid',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
