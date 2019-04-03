<?php

declare(strict_types=1);

use buzzingpixel\corbomitemailer\EmailApi;
use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use corbomite\events\EventDispatcher;
use corbomite\queue\QueueApi;
use Psr\Container\ContainerInterface;
use src\app\pings\interfaces\PingApiInterface;
use src\app\pings\listeners\ProjectArchiveListener;
use src\app\pings\listeners\ProjectDeleteListener;
use src\app\pings\listeners\ProjectUnArchiveListener;
use src\app\pings\PingApi;
use src\app\pings\schedules\CheckPingsSchedule;
use src\app\pings\services\ArchivePingService;
use src\app\pings\services\DeletePingService;
use src\app\pings\services\FetchPingService;
use src\app\pings\services\SavePingService;
use src\app\pings\services\UnArchivePingService;
use src\app\pings\tasks\CheckPingTask;
use src\app\pings\tasks\CollectPingsForQueueTask;

return [
    PingApi::class => static function (ContainerInterface $di) {
        return new PingApi($di);
    },
    PingApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(PingApi::class);
    },
    ArchivePingService::class => static function (ContainerInterface $di) {
        return new ArchivePingService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    DeletePingService::class => static function (ContainerInterface $di) {
        return new DeletePingService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    FetchPingService::class => static function (ContainerInterface $di) {
        return new FetchPingService(
            $di->get(BuildQueryService::class)
        );
    },
    SavePingService::class => static function (ContainerInterface $di) {
        return new SavePingService(
            new Slugify(),
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get('UuidFactoryWithOrderedTimeCodec'),
            $di->get(EventDispatcher::class)
        );
    },
    UnArchivePingService::class => static function (ContainerInterface $di) {
        return new UnArchivePingService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    ProjectArchiveListener::class => static function (ContainerInterface $di) {
        return new ProjectArchiveListener(
            $di->get(PingApi::class)
        );
    },
    ProjectDeleteListener::class => static function (ContainerInterface $di) {
        return new ProjectDeleteListener(
            $di->get(PingApi::class)
        );
    },
    ProjectUnArchiveListener::class => static function (ContainerInterface $di) {
        return new ProjectUnArchiveListener(
            $di->get(PingApi::class)
        );
    },
    CheckPingsSchedule::class => static function (ContainerInterface $di) {
        return new CheckPingsSchedule(
            $di->get(QueueApi::class)
        );
    },
    CollectPingsForQueueTask::class => static function (ContainerInterface $di) {
        return new CollectPingsForQueueTask(
            $di->get(PingApi::class),
            $di->get(QueueApi::class)
        );
    },
    CheckPingTask::class => static function (ContainerInterface $di) {
        return new CheckPingTask(
            $di->get(PingApi::class),
            $di->get(EmailApi::class)
        );
    },
];
