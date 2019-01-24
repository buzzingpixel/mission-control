<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use src\app\reminders\ReminderApi;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\reminders\services\SaveReminderService;
use src\app\reminders\services\FetchReminderService;
use src\app\reminders\services\ArchiveReminderService;

return [
    ReminderApi::class => function () {
        return new ReminderApi(new Di());
    },
    ArchiveReminderService::class => function () {
        return new ArchiveReminderService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    FetchReminderService::class => function () {
        return new FetchReminderService(Di::get(BuildQueryService::class));
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
