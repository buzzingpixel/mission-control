<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\monitoredurls\MonitoredUrlsApi;

return [
    MonitoredUrlsApi::class => function () {
        return new MonitoredUrlsApi(new Di());
    },
];
