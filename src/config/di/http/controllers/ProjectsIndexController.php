<?php
declare(strict_types=1);

use corbomite\di\Di;
use Zend\Diactoros\Response;
use corbomite\twig\TwigEnvironment;
use src\app\http\services\RequireLoginService;
use src\app\http\controllers\ProjectsIndexController;

return [
    ProjectsIndexController::class => function () {
        return new ProjectsIndexController(
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
];
