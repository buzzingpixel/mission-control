<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\notificationemails\NotificationEmailsApi;

return [
    NotificationEmailsApi::class => function () {
        return new NotificationEmailsApi(
            new Di()
        );
    },
];
