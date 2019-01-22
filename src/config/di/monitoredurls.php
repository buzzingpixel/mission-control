<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\UuidFactory;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as DbFactory;
use corbomite\db\Factory as OrmFactory;
use src\app\monitoredurls\MonitoredUrlsApi;
use corbomite\db\services\BuildQueryService;
use src\app\monitoredurls\services\SaveIncidentService;
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
            new Di(),
            new DbFactory()
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
            new UuidFactory(),
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
];
