<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use src\app\projects\ProjectsApi;
use corbomite\twig\TwigEnvironment;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\http\services\RequireLoginService;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\CreateProjectController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\RenderErrorPageController;
use src\app\http\controllers\MonitoredUrlIndexController;
use src\app\http\controllers\CreateMonitoredUrlController;

return [
    CreateMonitoredUrlController::class => function () {
        return new CreateMonitoredUrlController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    CreateProjectController::class => function () {
        return new CreateProjectController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    EditProjectController::class => function () {
        return new EditProjectController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
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
    MonitoredUrlIndexController::class => function () {
        return new MonitoredUrlIndexController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
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
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
    RenderErrorPageController::class => function () {
        return new RenderErrorPageController(
            Di::get(TwigEnvironment::class),
            new Response()
        );
    },
    ViewProjectController::class => function () {
        return new ViewProjectController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class)
        );
    },
];
