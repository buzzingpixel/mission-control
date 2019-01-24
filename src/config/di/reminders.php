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
use src\app\reminders\services\DeleteReminderService;
use src\app\reminders\services\ArchiveReminderService;
use src\app\reminders\listeners\ProjectDeleteListener;
use src\app\reminders\listeners\ProjectArchiveListener;
use src\app\reminders\services\UnArchiveReminderService;
use src\app\reminders\listeners\ProjectUnArchiveListener;

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
    DeleteReminderService::class => function () {
        return new DeleteReminderService(
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
    UnArchiveReminderService::class => function () {
        return new UnArchiveReminderService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    ProjectArchiveListener::class => function () {
        return new ProjectArchiveListener(
            Di::get(ReminderApi::class)
        );
    },
    ProjectDeleteListener::class => function () {
        return new ProjectDeleteListener(
            Di::get(ReminderApi::class)
        );
    },
    ProjectUnArchiveListener::class => function () {
        return new ProjectUnArchiveListener(
            Di::get(ReminderApi::class)
        );
    },
];
