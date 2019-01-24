<?php
declare(strict_types=1);

use src\app\monitoredurls\schedules\CheckUrlsSchedule;

return [
    [
        'class' => CheckUrlsSchedule::class,
        'runEvery' => 'Always'
    ],
];
