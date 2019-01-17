<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\twig\TwigEnvironment;
use src\app\http\services\RequireLoginService;
use src\app\http\controllers\CreateProjectController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\RenderErrorPageController;

return [
    CreateProjectController::class => function () {
        return new CreateProjectController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    ForgotPasswordController::class => function () {
        return new ForgotPasswordController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class)
        );
    },
    PasswordResetController::class => function () {
        return new PasswordResetController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class)
        );
    },
    ProjectsIndexController::class => function () {
        return new ProjectsIndexController(
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    RenderErrorPageController::class => function () {
        return new RenderErrorPageController(
            Di::get(TwigEnvironment::class),
            new Response()
        );
    },
];
