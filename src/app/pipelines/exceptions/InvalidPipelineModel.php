<?php

declare(strict_types=1);

namespace src\app\pipelines\exceptions;

use Exception;
use Throwable;

class InvalidPipelineModel extends Exception
{
    public function __construct(
        string $message = 'The pipeline model is not valid',
        int $code = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
