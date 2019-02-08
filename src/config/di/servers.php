<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use src\app\servers\ServerApi;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\servers\services\SaveServerService;

return [
    ServerApi::class => function () {
        return new ServerApi(
            Di::diContainer()
        );
    },
    SaveServerService::class => function () {
        return new SaveServerService(
            new Slugify(),
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
];
