<?php

declare(strict_types=1);

use src\app\pings\interfaces\PingApiInterface;
use src\app\pings\listeners\ProjectArchiveListener;
use src\app\pings\listeners\ProjectDeleteListener;
use src\app\pings\listeners\ProjectUnArchiveListener;
use src\app\pings\PingApi;
use src\app\pings\schedules\CheckPingsSchedule;
use src\app\pings\services\ArchivePingService;
use src\app\pings\services\DeletePingService;
use src\app\pings\services\FetchPingService;
use src\app\pings\services\SavePingService;
use src\app\pings\services\UnArchivePingService;
use src\app\pings\tasks\CheckPing;
use src\app\pings\tasks\CheckPingsTask;
use function DI\autowire;

return [
    PingApi::class => autowire(),
    PingApiInterface::class => autowire(PingApi::class),
    ArchivePingService::class => autowire(),
    DeletePingService::class => autowire(),
    FetchPingService::class => autowire(),
    SavePingService::class => autowire()
        ->constructorParameter(
            'uuidFactory',
            DI\get('UuidFactoryWithOrderedTimeCodec')
        ),
    UnArchivePingService::class => autowire(),
    ProjectArchiveListener::class => autowire(),
    ProjectDeleteListener::class => autowire(),
    ProjectUnArchiveListener::class => autowire(),
    CheckPingsSchedule::class => autowire(),
    CheckPingsTask::class => autowire(),
    CheckPing::class => autowire(),
];
