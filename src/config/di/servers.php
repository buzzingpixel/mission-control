<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\servers\ServerApi;

return [
    ServerApi::class => function () {
        return new ServerApi(
            Di::diContainer()
        );
    },
];
