<?php
declare(strict_types=1);

use src\app\utilities\TimeZoneListUtility;

return [
    TimeZoneListUtility::class => static function () {
        return new TimeZoneListUtility();
    },
];
