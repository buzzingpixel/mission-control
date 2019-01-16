<?php
declare(strict_types=1);

use corbomite\di\Di;
use Zend\Diactoros\Response;
use corbomite\twig\TwigEnvironment;
use src\app\http\controllers\RenderErrorPageController;

return [
    RenderErrorPageController::class => function () {
        return new RenderErrorPageController(
            Di::get(TwigEnvironment::class),
            new Response()
        );
    },
];
