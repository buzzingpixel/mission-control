<?php

declare(strict_types=1);

use buzzingpixel\corbomitemailer\EmailApi;
use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\PDO;
use corbomite\db\services\BuildQueryService;
use corbomite\events\EventDispatcher;
use corbomite\queue\QueueApi;
use Psr\Container\ContainerInterface;
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
use src\app\support\extensions\GuzzleClientNoHttpErrors;

return [
    MonitoredUrlsApi::class => static function (ContainerInterface $di) {
        return new MonitoredUrlsApi($di);
    },
    MonitoredUrlsApiInterface::class => static function (ContainerInterface $di) {
        return $di->get(MonitoredUrlsApi::class);
    },
    ArchiveMonitoredUrlService::class => static function (ContainerInterface $di) {
        return new ArchiveMonitoredUrlService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    DeleteMonitoredUrlService::class => static function (ContainerInterface $di) {
        return new DeleteMonitoredUrlService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    FetchIncidentsService::class => static function (ContainerInterface $di) {
        return new FetchIncidentsService(
            $di->get(BuildQueryService::class)
        );
    },
    FetchMonitoredUrlsService::class => static function (ContainerInterface $di) {
        return new FetchMonitoredUrlsService(
            $di->get(BuildQueryService::class)
        );
    },
    SaveIncidentService::class => static function (ContainerInterface $di) {
        return new SaveIncidentService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    SaveMonitoredUrlService::class => static function (ContainerInterface $di) {
        return new SaveMonitoredUrlService(
            new Slugify(),
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    UnArchiveMonitoredUrlService::class => static function (ContainerInterface $di) {
        return new UnArchiveMonitoredUrlService(
            new OrmFactory(),
            $di->get(BuildQueryService::class),
            $di->get(EventDispatcher::class)
        );
    },
    MonitoredUrlDeleteListener::class => static function (ContainerInterface $di) {
        return new MonitoredUrlDeleteListener(
            $di->get(PDO::class)
        );
    },
    ProjectArchiveListener::class => static function (ContainerInterface $di) {
        return new ProjectArchiveListener(
            $di->get(MonitoredUrlsApi::class)
        );
    },
    ProjectUnArchiveListener::class => static function (ContainerInterface $di) {
        return new ProjectUnArchiveListener(
            $di->get(MonitoredUrlsApi::class)
        );
    },
    ProjectDeleteListener::class => static function (ContainerInterface $di) {
        return new ProjectDeleteListener(
            $di->get(MonitoredUrlsApi::class)
        );
    },
    CheckUrlsSchedule::class => static function (ContainerInterface $di) {
        return new CheckUrlsSchedule(
            $di->get(QueueApi::class)
        );
    },
    CollectUrlsForQueueTask::class => static function (ContainerInterface $di) {
        return new CollectUrlsForQueueTask(
            $di->get(QueueApi::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    CheckUrlTask::class => static function (ContainerInterface $di) {
        return new CheckUrlTask(
            $di->get(EmailApi::class),
            new GuzzleClientNoHttpErrors(),
            $di->get(MonitoredUrlsApi::class)
        );
    },
];
