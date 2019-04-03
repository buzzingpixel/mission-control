<?php

declare(strict_types=1);

use src\app\cli\actions\DemoteUserFromAdminAction;
use src\app\cli\actions\PromoteUserToAdminAction;
use function DI\autowire;

return [
    PromoteUserToAdminAction::class => autowire(),
    DemoteUserFromAdminAction::class => autowire(),
];
