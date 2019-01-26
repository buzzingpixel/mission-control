<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\notificationemails\services\SaveNotificationEmailService;
use src\app\notificationemails\services\FetchNotificationEmailService;

return [
    NotificationEmailsApi::class => function () {
        return new NotificationEmailsApi(
            new Di()
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
