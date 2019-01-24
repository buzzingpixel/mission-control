<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use corbomite\queue\QueueApi;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as DbFactory;
use corbomite\db\Factory as OrmFactory;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\monitoredurls\MonitoredUrlsApi;
use corbomite\db\services\BuildQueryService;
use src\app\monitoredurls\tasks\CheckUrlTask;
use src\app\monitoredurls\schedules\CheckUrlsSchedule;
use src\app\monitoredurls\services\SaveIncidentService;
use src\app\monitoredurls\tasks\CollectUrlsForQueueTask;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use src\app\monitoredurls\services\FetchIncidentsService;
use src\app\monitoredurls\listeners\ProjectDeleteListener;
use src\app\monitoredurls\listeners\ProjectArchiveListener;
use src\app\monitoredurls\services\SaveMonitoredUrlService;
use src\app\monitoredurls\listeners\ProjectUnArchiveListener;
use src\app\monitoredurls\services\DeleteMonitoredUrlService;
use src\app\monitoredurls\services\FetchMonitoredUrlsService;
use src\app\monitoredurls\services\ArchiveMonitoredUrlService;
use src\app\monitoredurls\services\UnArchiveMonitoredUrlService;

return [
    MonitoredUrlsApi::class => function () {
        return new MonitoredUrlsApi(
            new Di()
        );
    },
    ArchiveMonitoredUrlService::class => function () {
        return new ArchiveMonitoredUrlService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    DeleteMonitoredUrlService::class => function () {
        return new DeleteMonitoredUrlService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    FetchIncidentsService::class => function () {
        return new FetchIncidentsService(
            Di::get(BuildQueryService::class)
        );
    },
    FetchMonitoredUrlsService::class => function () {
        return new FetchMonitoredUrlsService(
            Di::get(BuildQueryService::class)
        );
    },
    SaveIncidentService::class => function () {
        return new SaveIncidentService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    SaveMonitoredUrlService::class => function () {
        return new SaveMonitoredUrlService(
            new Slugify(),
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    UnArchiveMonitoredUrlService::class => function () {
        return new UnArchiveMonitoredUrlService(
            new OrmFactory(),
            Di::get(BuildQueryService::class),
            Di::get(EventDispatcher::class),
            new DbFactory()
        );
    },
    ProjectArchiveListener::class => function () {
        return new ProjectArchiveListener(
            Di::get(MonitoredUrlsApi::class)
        );
    },
    ProjectUnArchiveListener::class => function () {
        return new ProjectUnArchiveListener(
            Di::get(MonitoredUrlsApi::class)
        );
    },
    ProjectDeleteListener::class => function () {
        return new ProjectDeleteListener(
            Di::get(MonitoredUrlsApi::class)
        );
    },
    CheckUrlsSchedule::class => function () {
        return new CheckUrlsSchedule(
            Di::get(QueueApi::class)
        );
    },
    CollectUrlsForQueueTask::class => function () {
        return new CollectUrlsForQueueTask(
            Di::get(QueueApi::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    CheckUrlTask::class => function () {
        return new CheckUrlTask(
            Di::get(EmailApi::class),
            new GuzzleClientNoHttpErrors(),
            Di::get(MonitoredUrlsApi::class)
        );
    },
];
