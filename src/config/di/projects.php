<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use src\app\projects\ProjectsApi;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as DbFactory;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\projects\services\SaveProjectService;
use src\app\projects\services\DeleteProjectService;
use src\app\projects\services\FetchProjectsService;
use src\app\projects\services\ArchiveProjectService;
use src\app\projects\services\UnArchiveProjectService;

return [
    ProjectsApi::class => function () {
        return new ProjectsApi(
            new Di()
        );
    },
    ArchiveProjectService::class => function () {
        return new ArchiveProjectService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    DeleteProjectService::class => function () {
        return new DeleteProjectService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    FetchProjectsService::class => function () {
        return new FetchProjectsService(
            Di::get(BuildQueryService::class)
        );
    },
    SaveProjectService::class => function () {
        return new SaveProjectService(
            new Slugify(),
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    UnArchiveProjectService::class => function () {
        return new UnArchiveProjectService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
];
