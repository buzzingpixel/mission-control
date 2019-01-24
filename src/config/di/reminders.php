<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\reminders\ReminderApi;

return [
    ReminderApi::class => function () {
        return new ReminderApi(new Di());
    },
];
