<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\notificationemails\services\SaveNotificationEmailService;

return [
    NotificationEmailsApi::class => function () {
        return new NotificationEmailsApi(
            new Di()
        );
    },
    SaveNotificationEmailService::class => function () {
        return new SaveNotificationEmailService(
            new OrmFactory(),
            Di::get(BuildQueryService::class)
        );
    },
];
