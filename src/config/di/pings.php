<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\pings\PingApi;
use Cocur\Slugify\Slugify;
use corbomite\queue\QueueApi;
use corbomite\events\EventDispatcher;
use src\app\pings\tasks\CheckPingTask;
use corbomite\db\Factory as OrmFactory;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\pings\services\SavePingService;
use corbomite\db\services\BuildQueryService;
use src\app\pings\services\FetchPingService;
use src\app\pings\services\DeletePingService;
use src\app\pings\services\ArchivePingService;
use src\app\pings\schedules\CheckPingsSchedule;
use src\app\pings\services\UnArchivePingService;
use src\app\pings\tasks\CollectPingsForQueueTask;
use src\app\pings\listeners\ProjectDeleteListener;
use src\app\pings\listeners\ProjectArchiveListener;
use src\app\pings\listeners\ProjectUnArchiveListener;

return [
    PingApi::class => function () {
        return new PingApi(new Di());
    },
    ArchivePingService::class => function () {
        return new ArchivePingService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    DeletePingService::class => function () {
        return new DeletePingService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    FetchPingService::class => function () {
        return new FetchPingService(
            Di::get(BuildQueryService::class)
        );
    },
    SavePingService::class => function () {
        return new SavePingService(
            new Slugify(),
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get('UuidFactoryWithOrderedTimeCodec'),
            Di::get(EventDispatcher::class)
        );
    },
    UnArchivePingService::class => function () {
        return new UnArchivePingService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    ProjectArchiveListener::class => function () {
        return new ProjectArchiveListener(
            Di::get(PingApi::class)
        );
    },
    ProjectDeleteListener::class => function () {
        return new ProjectDeleteListener(
            Di::get(PingApi::class)
        );
    },
    ProjectUnArchiveListener::class => function () {
        return new ProjectUnArchiveListener(
            Di::get(PingApi::class)
        );
    },
    CheckPingsSchedule::class => function () {
        return new CheckPingsSchedule(
            Di::get(QueueApi::class)
        );
    },
    CollectPingsForQueueTask::class => function () {
        return new CollectPingsForQueueTask(
            Di::get(PingApi::class),
            Di::get(QueueApi::class)
        );
    },
    CheckPingTask::class => function () {
        return new CheckPingTask(
            Di::get(PingApi::class),
            Di::get(EmailApi::class)
        );
    },
];
