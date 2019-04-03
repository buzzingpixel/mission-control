<?php

declare(strict_types=1);

use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use corbomite\events\EventDispatcher;
use Psr\Container\ContainerInterface;
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

return [
    ReminderApi::class => static function (ContainerInterface $di) {
        return new ReminderApi($di);
    },
    ReminderApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(ReminderApi::class);
    },
    ArchiveReminderService::class => static function (ContainerInterface $di) {
        return new ArchiveReminderService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    DeleteReminderService::class => static function (ContainerInterface $di) {
        return new DeleteReminderService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    FetchReminderService::class => static function (ContainerInterface $di) {
        return new FetchReminderService($di->get(BuildQueryService::class));
    },
    SaveReminderService::class => static function (ContainerInterface $di) {
        return new SaveReminderService(
            new Slugify(),
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    UnArchiveReminderService::class => static function (ContainerInterface $di) {
        return new UnArchiveReminderService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    ProjectArchiveListener::class => static function (ContainerInterface $di) {
        return new ProjectArchiveListener(
            $di->get(ReminderApi::class)
        );
    },
    ProjectDeleteListener::class => static function (ContainerInterface $di) {
        return new ProjectDeleteListener(
            $di->get(ReminderApi::class)
        );
    },
    ProjectUnArchiveListener::class => static function (ContainerInterface $di) {
        return new ProjectUnArchiveListener(
            $di->get(ReminderApi::class)
        );
    },
];
