<?php

declare(strict_types=1);

namespace src\app\projects\exceptions;

use Exception;
use Throwable;

class ProjectNameNotUniqueException extends Exception
{
    public function __construct(
        string $message = 'The project name is not unique',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
