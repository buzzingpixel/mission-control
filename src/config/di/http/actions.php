<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\user\UserApi;
use Zend\Diactoros\Response;
use src\app\projects\ProjectsApi;
use corbomite\http\RequestHelper;
use corbomite\flashdata\FlashDataApi;
use src\app\http\actions\LogOutAction;
use corbomite\requestdatastore\DataStore;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\http\actions\EditProjectAction;
use src\app\http\actions\ProjectListActions;
use src\app\http\actions\CreateProjectAction;
use src\app\http\actions\ResetPasswordAction;
use src\app\http\actions\CreateMonitoredUrlAction;
use src\app\http\actions\SendPasswordResetEmailAction;

return [
    CreateMonitoredUrlAction::class => function () {
        return new CreateMonitoredUrlAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    CreateProjectAction::class => function () {
        return new CreateProjectAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ProjectsApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    EditProjectAction::class => function () {
        return new EditProjectAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(DataStore::class),
            Di::get(ProjectsApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    LogOutAction::class => function () {
        return new LogOutAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class)
        );
    },
    ProjectListActions::class => function () {
        return new ProjectListActions(
            Di::get(UserApi::class),
            new Response(),
            Di::get(ProjectsApi::class),
            Di::get(FlashDataApi::class),
            Di::get(RequestHelper::class)
        );
    },
    ResetPasswordAction::class => function () {
        return new ResetPasswordAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(FlashDataApi::class)
        );
    },
    SendPasswordResetEmailAction::class => function () {
        return new SendPasswordResetEmailAction(
            Di::get(UserApi::class),
            new Response(),
            Di::get(EmailApi::class)
        );
    },
];
