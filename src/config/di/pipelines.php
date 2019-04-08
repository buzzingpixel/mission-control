<?php

declare(strict_types=1);

use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\listeners\ProjectArchiveListener;
use src\app\pipelines\listeners\ProjectDeleteListener;
use src\app\pipelines\listeners\ProjectUnArchiveListener;
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
use function DI\autowire;

return [
    PipelineApi::class => autowire(),
    PipelineApiInterface::class => autowire(PipelineApi::class),
    ArchivePipelineService::class => autowire(),
    DeletePipelineService::class => autowire(),
    FetchOnePipelineJobItemService::class => autowire(),
    FetchPipelineJobService::class => autowire(),
    FetchPipelineService::class => autowire(),
    InitJobFromPipelineModelService::class => autowire(),
    ProjectArchiveListener::class => autowire(),
    ProjectDeleteListener::class => autowire(),
    ProjectUnArchiveListener::class => autowire(),
    SavePipelineJobService::class => autowire(),
    SavePipelineService::class => autowire()
        ->constructorParameter(
            'uuidFactory',
            DI\get('UuidFactoryWithOrderedTimeCodec')
        ),
    UnArchivePipelineService::class => autowire(),
    SavePipelineJobListener::class => autowire(),
    RunJobItemTask::class => autowire()
        ->constructorParameter(
            'sendNotificationAdapters',
            DI\get('NotificationAdaptersArray')
        ),
];
