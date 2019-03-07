<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\services\BuildQueryService;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\notificationemails\services\SaveNotificationEmailService;
use src\app\notificationemails\services\FetchNotificationEmailService;
use src\app\notificationemails\services\DeleteNotificationEmailService;
use src\app\notificationemails\services\EnableNotificationEmailService;
use src\app\notificationemails\services\DisableNotificationEmailService;

return [
    NotificationEmailsApi::class => static function (ContainerInterface $di) {
        return new NotificationEmailsApi($di);
    },
    DeleteNotificationEmailService::class => static function (ContainerInterface $di) {
        return new DeleteNotificationEmailService(
            new OrmFactory(),
            $di->get(BuildQueryService::class)
        );
    },
    DisableNotificationEmailService::class => static function (ContainerInterface $di) {
        return new DisableNotificationEmailService(
            new OrmFactory(),
            $di->get(BuildQueryService::class)
        );
    },
    EnableNotificationEmailService::class => static function (ContainerInterface $di) {
        return new EnableNotificationEmailService(
            new OrmFactory(),
            $di->get(BuildQueryService::class)
        );
    },
    FetchNotificationEmailService::class => static function (ContainerInterface $di) {
        return new FetchNotificationEmailService(
            $di->get(BuildQueryService::class)
        );
    },
    SaveNotificationEmailService::class => static function (ContainerInterface $di) {
        return new SaveNotificationEmailService(
            new OrmFactory(),
            $di->get(BuildQueryService::class)
        );
    },
];
