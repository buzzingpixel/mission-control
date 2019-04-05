<?php

declare(strict_types=1);

use src\app\utilities\TimeZoneListUtility;
use function DI\autowire;

return [
    TimeZoneListUtility::class => autowire(),
];
