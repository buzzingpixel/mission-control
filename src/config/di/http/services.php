<?php

declare(strict_types=1);

use src\app\http\services\RenderPipelineInnerComponents;
use src\app\http\services\RequireLoginService;
use function DI\autowire;

return [
    RequireLoginService::class => autowire(),
    RenderPipelineInnerComponents::class => autowire(),
];
