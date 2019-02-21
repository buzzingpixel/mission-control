<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use src\app\servers\ServerApi;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\servers\services\SaveServerService;
use src\app\pings\services\DeleteSSHKeyService;
use src\app\servers\services\SaveSSHKeyService;
use src\app\pings\services\DeleteServerService;
use src\app\servers\services\FetchSSHKeyService;
use src\app\servers\services\FetchServerService;
use src\app\servers\services\ArchiveServerService;
use src\app\servers\services\ArchiveSSHKeyService;
use src\app\servers\services\UnArchiveServerService;
use src\app\servers\services\UnArchiveSSHKeyService;

return [
    ServerApi::class => function () {
        return new ServerApi(
            Di::diContainer()
        );
    },
    ArchiveServerService::class => function () {
        return new ArchiveServerService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    ArchiveSSHKeyService::class => function () {
        return new ArchiveSSHKeyService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    DeleteServerService::class => function () {
        return new DeleteServerService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    DeleteSSHKeyService:: class => function () {
        return new DeleteSSHKeyService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    FetchServerService::class => function () {
        return new FetchServerService(
            Di::diContainer(),
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(FetchSSHKeyService::class)
        );
    },
    FetchSSHKeyService::class => function () {
        return new FetchSSHKeyService(
            Di::get(BuildQueryService::class)
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
    SaveSSHKeyService::class => function () {
        return new SaveSSHKeyService(
            new Slugify(),
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    UnArchiveServerService::class => function () {
        return new UnArchiveServerService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
    UnArchiveSSHKeyService::class => function () {
        return new UnArchiveSSHKeyService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class)
        );
    },
];
