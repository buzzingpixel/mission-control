<?php

declare(strict_types=1);

namespace src\app\tickets\exceptions;

use Exception;
use Throwable;

class InvalidModel extends Exception
{
    public function __construct(
        string $message = 'The model is invalid',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
