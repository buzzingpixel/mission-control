<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use corbomite\queue\QueueApi;
use src\app\projects\ProjectsApi;
use corbomite\twig\TwigEnvironment;
use src\app\utilities\TimeZoneListUtility;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\http\controllers\AdminController;
use src\app\http\services\RequireLoginService;
use src\app\http\controllers\AccountController;
use src\app\http\controllers\CreateUserController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\CreateProjectController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\ChangePasswordController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\RenderErrorPageController;
use src\app\http\controllers\EditMonitoredUrlController;
use src\app\http\controllers\ViewMonitoredUrlController;
use src\app\http\controllers\MonitoredUrlIndexController;
use src\app\http\controllers\CreateMonitoredUrlController;

return [
    AccountController::class => function () {
        return new AccountController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class),
            Di::get(TimeZoneListUtility::class)
        );
    },
    AdminController::class => function () {
        return new AdminController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(QueueApi::class),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    ChangePasswordController::class => function () {
        return new ChangePasswordController(
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
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
    CreateUserController::class => function () {
        return new CreateUserController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class)
        );
    },
    EditMonitoredUrlController::class => function () {
        return new EditMonitoredUrlController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
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
    ViewMonitoredUrlController::class => function () {
        return new ViewMonitoredUrlController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    ViewProjectController::class => function () {
        return new ViewProjectController(
            Di::get(UserApi::class),
            new Response(),
            Di::get(TwigEnvironment::class),
            Di::get(ProjectsApi::class),
            Di::get(RequireLoginService::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
];
