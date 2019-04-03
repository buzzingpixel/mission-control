<?php

declare(strict_types=1);

use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use corbomite\events\EventDispatcher;
use Psr\Container\ContainerInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use src\app\projects\ProjectsApi;
use src\app\projects\services\ArchiveProjectService;
use src\app\projects\services\DeleteProjectService;
use src\app\projects\services\FetchProjectsService;
use src\app\projects\services\SaveProjectService;
use src\app\projects\services\UnArchiveProjectService;

return [
    ProjectsApi::class => static function (ContainerInterface $di) {
        return new ProjectsApi($di);
    },
    ProjectsApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(ProjectsApi::class);
    },
    ArchiveProjectService::class => static function (ContainerInterface $di) {
        return new ArchiveProjectService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    DeleteProjectService::class => static function (ContainerInterface $di) {
        return new DeleteProjectService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    FetchProjectsService::class => static function (ContainerInterface $di) {
        return new FetchProjectsService(
            $di->get(BuildQueryService::class)
        );
    },
    SaveProjectService::class => static function (ContainerInterface $di) {
        return new SaveProjectService(
            new Slugify(),
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    UnArchiveProjectService::class => static function (ContainerInterface $di) {
        return new UnArchiveProjectService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
];
