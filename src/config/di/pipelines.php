<?php
declare(strict_types=1);

use Cocur\Slugify\Slugify;
use src\app\pipelines\PipelineApi;
use Psr\Container\ContainerInterface;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\pipelines\services\SavePipelineService;
use src\app\pipelines\services\FetchPipelineService;
use src\app\pipelines\services\DeletePipelineService;
use src\app\pipelines\services\SavePipelineJobService;
use src\app\pipelines\services\ArchivePipelineService;
use src\app\pipelines\services\UnArchivePipelineService;

return [
    PipelineApi::class => static function (ContainerInterface $di) {
        return new PipelineApi($di);
    },
    ArchivePipelineService::class => static function (ContainerInterface $di) {
        return new ArchivePipelineService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    DeletePipelineService::class => static function (ContainerInterface $di) {
        return new DeletePipelineService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    FetchPipelineService::class => static function (ContainerInterface $di) {
        return new FetchPipelineService(
            $di->get(BuildQueryService::class)
        );
    },
    SavePipelineJobService::class => static function (ContainerInterface $di) {
        return new SavePipelineJobService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    SavePipelineService::class => static function (ContainerInterface $di) {
        return new SavePipelineService(
            new Slugify(),
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get('UuidFactoryWithOrderedTimeCodec'),
            $di->get(EventDispatcher::class)
        );
    },
    UnArchivePipelineService::class => static function (ContainerInterface $di) {
        return new UnArchivePipelineService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    }
];
