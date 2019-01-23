<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\pings\PingApi;

return [
    PingApi::class => function () {
        return new PingApi(new Di());
    },
];
