<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\db\PDO;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\UuidFactory;
use src\app\projects\ProjectsApi;
use src\app\datasupport\BuildQuery;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\projects\services\SaveProjectService;
use src\app\projects\services\FetchProjectsService;
use src\app\projects\services\ArchiveProjectService;

return [
    ProjectsApi::class => function () {
        return new ProjectsApi(new Di());
    },
    ArchiveProjectService::class => function () {
        return new ArchiveProjectService(
            Di::get(PDO::class),
            new OrmFactory(),
            Di::get(BuildQuery::class),
            Di::get(EventDispatcher::class),
            new FetchDataParamsFactory()
        );
    },
    FetchProjectsService::class => function () {
        return new FetchProjectsService(
            Di::get(BuildQuery::class)
        );
    },
    SaveProjectService::class => function () {
        return new SaveProjectService(
            new Slugify(),
            new OrmFactory(),
            new UuidFactory(),
            Di::get(BuildQuery::class),
            Di::get(EventDispatcher::class),
            new FetchDataParamsFactory()
        );
    },
];
