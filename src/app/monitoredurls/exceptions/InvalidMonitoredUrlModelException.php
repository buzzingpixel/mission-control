<?php

declare(strict_types=1);

namespace src\app\monitoredurls\exceptions;

use Exception;
use Throwable;

class InvalidMonitoredUrlModelException extends Exception
{
    public function __construct(
        string $message = 'The monitored url model is not valid',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
