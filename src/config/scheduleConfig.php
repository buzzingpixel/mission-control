<?php

declare(strict_types=1);

use src\app\monitoredurls\schedules\CheckUrlsSchedule;
use src\app\notifications\schedules\CheckPingsForNotificationsSchedule;
use src\app\notifications\schedules\CheckRemindersForNotificationsSchedule;
use src\app\notifications\schedules\CheckUrlsForNotificationsSchedule;
use src\app\pings\schedules\CheckPingsSchedule;

return [
    [
        'class' => CheckUrlsSchedule::class,
        'runEvery' => 'Always',
    ],
    [
        'class' => CheckPingsSchedule::class,
        'runEvery' => 'Always',
    ],
    [
        'class' => CheckUrlsForNotificationsSchedule::class,
        'runEvery' => 'Always',
    ],
    [
        'class' => CheckPingsForNotificationsSchedule::class,
        'runEvery' => 'Always',
    ],
    [
        'class' => CheckRemindersForNotificationsSchedule::class,
        'runEvery' => 'DayAtMidnight',
    ],
];
