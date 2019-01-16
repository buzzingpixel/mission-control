<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\http\middlewares\ErrorPagesMiddleware;
use src\app\http\controllers\RenderErrorPageController;

return [
    ErrorPagesMiddleware::class => function () {
        return new ErrorPagesMiddleware(
            Di::get(RenderErrorPageController::class)
        );
    },
];
