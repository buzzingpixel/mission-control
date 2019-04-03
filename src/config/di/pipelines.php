<?php

declare(strict_types=1);

use Atlas\Pdo\Connection;
use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use corbomite\events\EventDispatcher;
use corbomite\queue\QueueApi;
use Psr\Container\ContainerInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\listeners\SavePipelineJobListener;
use src\app\pipelines\PipelineApi;
use src\app\pipelines\services\ArchivePipelineService;
use src\app\pipelines\services\DeletePipelineService;
use src\app\pipelines\services\FetchOnePipelineJobItemService;
use src\app\pipelines\services\FetchPipelineJobService;
use src\app\pipelines\services\FetchPipelineService;
use src\app\pipelines\services\InitJobFromPipelineModelService;
use src\app\pipelines\services\SavePipelineJobService;
use src\app\pipelines\services\SavePipelineService;
use src\app\pipelines\services\UnArchivePipelineService;
use src\app\pipelines\tasks\RunJobItemTask;
use src\app\pipelines\transformers\PipelineJobItemRecordModelTransformer;
use src\app\pipelines\transformers\PipelineJobRecordModelTransformer;
use src\app\pipelines\transformers\PipelineRecordModelTransformer;

return [
    PipelineApi::class => static function (ContainerInterface $di) {
        return new PipelineApi($di);
    },
    PipelineApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(PipelineApi::class);
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
    FetchOnePipelineJobItemService::class => static function (ContainerInterface $di) {
        return new FetchOnePipelineJobItemService(
            $di->get(BuildQueryService::class),
            $di->get(PipelineJobRecordModelTransformer::class),
            $di->get(PipelineJobItemRecordModelTransformer::class)
        );
    },
    FetchPipelineJobService::class => static function (ContainerInterface $di) {
        return new FetchPipelineJobService(
            $di->get(BuildQueryService::class),
            $di->get(PipelineJobRecordModelTransformer::class)
        );
    },
    FetchPipelineService::class => static function (ContainerInterface $di) {
        return new FetchPipelineService(
            $di->get(BuildQueryService::class),
            $di->get(PipelineRecordModelTransformer::class)
        );
    },
    InitJobFromPipelineModelService::class => static function (ContainerInterface $di) {
        return new InitJobFromPipelineModelService(
            $di->get(PipelineApi::class)
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
            $di->get(Connection::class),
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
    },
    SavePipelineJobListener::class => static function (ContainerInterface $di) {
        return new SavePipelineJobListener(
            $di->get(QueueApi::class)
        );
    },
    RunJobItemTask::class => static function (ContainerInterface $di) {
        return new RunJobItemTask($di->get(PipelineApi::class));
    },
];
