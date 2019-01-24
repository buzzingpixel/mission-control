<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use src\app\reminders\ReminderApi;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\reminders\services\SaveReminderService;

return [
    ReminderApi::class => function () {
        return new ReminderApi(new Di());
    },
    SaveReminderService::class => function () {
        return new SaveReminderService(
            new Slugify(),
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
];
