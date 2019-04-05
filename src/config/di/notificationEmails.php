<?php

declare(strict_types=1);

use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\notificationemails\services\DeleteNotificationEmailService;
use src\app\notificationemails\services\DisableNotificationEmailService;
use src\app\notificationemails\services\EnableNotificationEmailService;
use src\app\notificationemails\services\FetchNotificationEmailService;
use src\app\notificationemails\services\SaveNotificationEmailService;
use function DI\autowire;

return [
    NotificationEmailsApi::class => autowire(),
    NotificationEmailsApiInterface::class => autowire(NotificationEmailsApi::class),
    DeleteNotificationEmailService::class => autowire(),
    DisableNotificationEmailService::class => autowire(),
    EnableNotificationEmailService::class => autowire(),
    FetchNotificationEmailService::class => autowire(),
    SaveNotificationEmailService::class => autowire(),
];
