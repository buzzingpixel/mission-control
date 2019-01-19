<?php
declare(strict_types=1);

use src\app\utilities\TimeZoneListUtility;

return [
    TimeZoneListUtility::class => function () {
        return new TimeZoneListUtility();
    },
];
