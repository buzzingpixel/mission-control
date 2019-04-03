<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use src\app\http\controllers\RenderErrorPageController;
use src\app\http\middlewares\ErrorPagesMiddleware;

return [
    ErrorPagesMiddleware::class => static function (ContainerInterface $di) {
        return new ErrorPagesMiddleware(
            $di->get(RenderErrorPageController::class)
        );
    },
];
