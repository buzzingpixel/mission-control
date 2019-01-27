<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\notificationemails\services\SaveNotificationEmailService;
use src\app\notificationemails\services\FetchNotificationEmailService;
use src\app\notificationemails\services\EnableNotificationEmailService;
use src\app\notificationemails\services\DisableNotificationEmailService;

return [
    NotificationEmailsApi::class => function () {
        return new NotificationEmailsApi(
            new Di()
        );
    },
    DisableNotificationEmailService::class => function () {
        return new DisableNotificationEmailService(
            new OrmFactory(),
            Di::get(BuildQueryService::class)
        );
    },
    EnableNotificationEmailService::class => function () {
        return new EnableNotificationEmailService(
            new OrmFactory(),
            Di::get(BuildQueryService::class)
        );
    },
    FetchNotificationEmailService::class => function () {
        return new FetchNotificationEmailService(
            Di::get(BuildQueryService::class)
        );
    },
    SaveNotificationEmailService::class => function () {
        return new SaveNotificationEmailService(
            new OrmFactory(),
            Di::get(BuildQueryService::class)
        );
    },
];
