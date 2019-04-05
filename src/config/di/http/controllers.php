<?php

declare(strict_types=1);

use src\app\http\controllers\AccountController;
use src\app\http\controllers\AddNotificationEmailController;
use src\app\http\controllers\AdminController;
use src\app\http\controllers\ChangePasswordController;
use src\app\http\controllers\CreateMonitoredUrlController;
use src\app\http\controllers\CreatePingController;
use src\app\http\controllers\CreatePipelineController;
use src\app\http\controllers\CreateProjectController;
use src\app\http\controllers\CreateReminderController;
use src\app\http\controllers\CreateServerController;
use src\app\http\controllers\CreateSSHKeyController;
use src\app\http\controllers\CreateUserController;
use src\app\http\controllers\EditMonitoredUrlController;
use src\app\http\controllers\EditPingController;
use src\app\http\controllers\EditPipelineController;
use src\app\http\controllers\EditProjectController;
use src\app\http\controllers\EditReminderController;
use src\app\http\controllers\EditServerController;
use src\app\http\controllers\EditSSHKeyController;
use src\app\http\controllers\ForgotPasswordController;
use src\app\http\controllers\MonitoredUrlIndexController;
use src\app\http\controllers\PasswordResetController;
use src\app\http\controllers\PingCheckinController;
use src\app\http\controllers\PingIndexController;
use src\app\http\controllers\PipelineIndexController;
use src\app\http\controllers\ProjectsIndexController;
use src\app\http\controllers\RemindersIndexController;
use src\app\http\controllers\RenderErrorPageController;
use src\app\http\controllers\RunPipelineController;
use src\app\http\controllers\ServersIndexController;
use src\app\http\controllers\SSHKeyIndexController;
use src\app\http\controllers\ViewMonitoredUrlController;
use src\app\http\controllers\ViewPingController;
use src\app\http\controllers\ViewPipelineController;
use src\app\http\controllers\ViewPipelineJobDetailsController;
use src\app\http\controllers\ViewProjectController;
use src\app\http\controllers\ViewReminderController;
use src\app\http\controllers\ViewServerController;
use src\app\http\controllers\ViewSSHKeyController;
use function DI\autowire;

return [
    AccountController::class => autowire(),
    AddNotificationEmailController::class => autowire(),
    AdminController::class => autowire(),
    ChangePasswordController::class => autowire(),
    CreateMonitoredUrlController::class => autowire(),
    CreatePingController::class => autowire(),
    CreatePipelineController::class => autowire(),
    CreateProjectController::class => autowire(),
    CreateReminderController::class => autowire(),
    CreateServerController::class => autowire(),
    CreateSSHKeyController::class => autowire(),
    CreateUserController::class => autowire(),
    EditMonitoredUrlController::class => autowire(),
    EditPingController::class => autowire(),
    EditPipelineController::class => autowire(),
    EditProjectController::class => autowire(),
    EditReminderController::class => autowire(),
    EditServerController::class => autowire(),
    EditSSHKeyController::class => autowire(),
    ForgotPasswordController::class => autowire(),
    MonitoredUrlIndexController::class => autowire(),
    PasswordResetController::class => autowire(),
    PingCheckinController::class => autowire(),
    PingIndexController::class => autowire(),
    PipelineIndexController::class => autowire(),
    ProjectsIndexController::class => autowire(),
    RemindersIndexController::class => autowire(),
    RenderErrorPageController::class => autowire(),
    RunPipelineController::class => autowire(),
    ServersIndexController::class => autowire(),
    SSHKeyIndexController::class => autowire(),
    ViewMonitoredUrlController::class => autowire(),
    ViewPingController::class => autowire(),
    ViewPipelineController::class => autowire(),
    ViewPipelineJobDetailsController::class => autowire(),
    ViewProjectController::class => autowire(),
    ViewReminderController::class => autowire(),
    ViewServerController::class => autowire(),
    ViewSSHKeyController::class => autowire(),
];
