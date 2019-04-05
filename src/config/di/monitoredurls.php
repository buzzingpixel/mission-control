<?php

declare(strict_types=1);

use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\monitoredurls\listeners\MonitoredUrlDeleteListener;
use src\app\monitoredurls\listeners\ProjectArchiveListener;
use src\app\monitoredurls\listeners\ProjectDeleteListener;
use src\app\monitoredurls\listeners\ProjectUnArchiveListener;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\monitoredurls\schedules\CheckUrlsSchedule;
use src\app\monitoredurls\services\ArchiveMonitoredUrlService;
use src\app\monitoredurls\services\DeleteMonitoredUrlService;
use src\app\monitoredurls\services\FetchIncidentsService;
use src\app\monitoredurls\services\FetchMonitoredUrlsService;
use src\app\monitoredurls\services\SaveIncidentService;
use src\app\monitoredurls\services\SaveMonitoredUrlService;
use src\app\monitoredurls\services\UnArchiveMonitoredUrlService;
use src\app\monitoredurls\tasks\CheckUrlTask;
use src\app\monitoredurls\tasks\CollectUrlsForQueueTask;
use function DI\autowire;

return [
    MonitoredUrlsApi::class => autowire(),
    MonitoredUrlsApiInterface::class => autowire(MonitoredUrlsApi::class),
    ArchiveMonitoredUrlService::class => autowire(),
    DeleteMonitoredUrlService::class => autowire(),
    FetchIncidentsService::class => autowire(),
    FetchMonitoredUrlsService::class => autowire(),
    SaveIncidentService::class => autowire(),
    SaveMonitoredUrlService::class => autowire(),
    UnArchiveMonitoredUrlService::class => autowire(),
    MonitoredUrlDeleteListener::class => autowire(),
    ProjectArchiveListener::class => autowire(),
    ProjectUnArchiveListener::class => autowire(),
    ProjectDeleteListener::class => autowire(),
    CheckUrlsSchedule::class => autowire(),
    CollectUrlsForQueueTask::class => autowire(),
    CheckUrlTask::class => autowire(),
];
