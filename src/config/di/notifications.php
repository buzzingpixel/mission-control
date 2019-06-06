<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use src\app\notifications\notificationadapters\SendEmailNotificationAdapter;
use src\app\notifications\notificationadapters\SlackNotificationAdapter;
use src\app\notifications\schedules\CheckPingsForNotificationsSchedule;
use src\app\notifications\schedules\CheckRemindersForNotificationsSchedule;
use src\app\notifications\schedules\CheckUrlsForNotificationsSchedule;
use src\app\notifications\tasks\CheckPingForNotification;
use src\app\notifications\tasks\CheckPingsForNotificationsTask;
use src\app\notifications\tasks\CheckReminderForNotification;
use src\app\notifications\tasks\CheckRemindersForNotificationsTask;
use src\app\notifications\tasks\CheckUrlForNotification;
use src\app\notifications\tasks\CheckUrlsForNotificationsTask;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use function DI\autowire;

return [
    'NotificationAdaptersArray' => static function (ContainerInterface $di) {
        return [
            $di->get(SendEmailNotificationAdapter::class),
            $di->get(SlackNotificationAdapter::class),
        ];
    },
    'SlackNotificationAdapterOnlyArray' => static function (ContainerInterface $di) {
        return [$di->get(SlackNotificationAdapter::class)];
    },
    CheckPingsForNotificationsSchedule::class => autowire(),
    CheckRemindersForNotificationsSchedule::class => autowire(),
    CheckUrlsForNotificationsSchedule::class => autowire(),
    CheckPingForNotification::class => autowire()
        ->constructorParameter(
            'sendNotificationAdapters',
            DI\get('NotificationAdaptersArray')
        ),
    CheckReminderForNotification::class => autowire()
        ->constructorParameter(
            'sendNotificationAdapters',
            DI\get('NotificationAdaptersArray')
        ),
    CheckUrlForNotification::class => autowire()
        ->constructorParameter(
            'sendNotificationAdapters',
            DI\get('NotificationAdaptersArray')
        ),
    CheckPingsForNotificationsTask::class => autowire(),
    CheckRemindersForNotificationsTask::class => autowire(),
    CheckUrlsForNotificationsTask::class => autowire(),
    SendEmailNotificationAdapter::class => autowire(),
    SlackNotificationAdapter::class => static function (ContainerInterface $di) {
        return new SlackNotificationAdapter(
            new GuzzleClientNoHttpErrors(),
            getenv('SLACK_NOTIFICATION_WEBHOOK_URL') ?: null
        );
    },
];
