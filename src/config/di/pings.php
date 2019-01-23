<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\pings\PingApi;
use Cocur\Slugify\Slugify;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\pings\services\SavePingService;
use corbomite\db\services\BuildQueryService;
use src\app\pings\services\FetchPingService;
use src\app\pings\services\DeletePingService;
use src\app\pings\services\ArchivePingService;
use src\app\pings\services\UnArchivePingService;

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
];
