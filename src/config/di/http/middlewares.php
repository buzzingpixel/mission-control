<?php

declare(strict_types=1);

use src\app\http\middlewares\ErrorPagesMiddleware;
use function DI\autowire;

return [
    ErrorPagesMiddleware::class => autowire(),
];
