<?php

declare(strict_types=1);

namespace src\app\servers\exceptions;

use Exception;
use Throwable;

class InvalidServerModelException extends Exception
{
    public function __construct(
        string $message = 'The server model is not valid',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
