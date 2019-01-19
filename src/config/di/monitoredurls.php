<?php
declare(strict_types=1);

use corbomite\di\Di;
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\UuidFactory;
use src\app\datasupport\BuildQuery;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\monitoredurls\services\SaveMonitoredUrlService;

return [
    MonitoredUrlsApi::class => function () {
        return new MonitoredUrlsApi(new Di());
    },
    SaveMonitoredUrlService::class => function () {
        return new SaveMonitoredUrlService(
            new Slugify(),
            new OrmFactory(),
            new UuidFactory(),
            Di::get(BuildQuery::class),
            Di::get(EventDispatcher::class),
            new FetchDataParamsFactory()
        );
    },
];
