<?php

declare(strict_types=1);

use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\reminders\listeners\ProjectArchiveListener;
use src\app\reminders\listeners\ProjectDeleteListener;
use src\app\reminders\listeners\ProjectUnArchiveListener;
use src\app\reminders\ReminderApi;
use src\app\reminders\services\ArchiveReminderService;
use src\app\reminders\services\DeleteReminderService;
use src\app\reminders\services\FetchReminderService;
use src\app\reminders\services\SaveReminderService;
use src\app\reminders\services\UnArchiveReminderService;
use function DI\autowire;

return [
    ReminderApi::class => autowire(),
    ReminderApiInterface::class => autowire(ReminderApi::class),
    ArchiveReminderService::class => autowire(),
    DeleteReminderService::class => autowire(),
    FetchReminderService::class => autowire(),
    SaveReminderService::class => autowire(),
    UnArchiveReminderService::class => autowire(),
    ProjectArchiveListener::class => autowire(),
    ProjectDeleteListener::class => autowire(),
    ProjectUnArchiveListener::class => autowire(),
];
