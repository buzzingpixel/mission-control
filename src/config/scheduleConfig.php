<?php
declare(strict_types=1);

use src\app\pings\schedules\CheckPingsSchedule;
use src\app\monitoredurls\schedules\CheckUrlsSchedule;

return [
    [
        'class' => CheckUrlsSchedule::class,
        'runEvery' => 'Always'
    ],
    [
        'class' => CheckPingsSchedule::class,
        'runEvery' => 'Always'
    ],
];
