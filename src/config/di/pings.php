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

return [
    PingApi::class => function () {
        return new PingApi(new Di());
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
];
