<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\notifications\notificationadapters\SendEmailNotificationAdapter;
use src\app\notifications\notificationadapters\SlackNotificationAdapter;
use src\app\notifications\schedules\CheckPingsForNotificationsSchedule;
use src\app\notifications\schedules\CheckRemindersForNotificationsSchedule;
use src\app\notifications\schedules\CheckUrlsForNotificationsSchedule;
use src\app\notifications\tasks\CheckPingForNotificationTask;
use src\app\notifications\tasks\CheckReminderForNotificationTask;
use src\app\notifications\tasks\CheckUrlForNotificationTask;
use src\app\notifications\tasks\CollectPingsForNotificationQueueTask;
use src\app\notifications\tasks\CollectRemindersForNotificationQueueTask;
use src\app\notifications\tasks\CollectUrlsForNotificationQueueTask;
use src\app\pings\PingApi;
use src\app\reminders\ReminderApi;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use function DI\autowire;

return [
    'NotificationAdaptersArray' => static function (ContainerInterface $di) {
        return [
            $di->get(SendEmailNotificationAdapter::class),
            $di->get(SlackNotificationAdapter::class),
        ];
    },
    CheckPingsForNotificationsSchedule::class => autowire(),
    CheckRemindersForNotificationsSchedule::class => autowire(),
    CheckUrlsForNotificationsSchedule::class => autowire(),
    CheckPingForNotificationTask::class => static function (ContainerInterface $di) {
        return new CheckPingForNotificationTask(
            $di->get(PingApi::class),
            $di->get('NotificationAdaptersArray')
        );
    },
    CheckReminderForNotificationTask::class => static function (ContainerInterface $di) {
        return new CheckReminderForNotificationTask(
            $di->get(ReminderApi::class),
            $di->get('NotificationAdaptersArray')
        );
    },
    CheckUrlForNotificationTask::class => static function (ContainerInterface $di) {
        return new CheckUrlForNotificationTask(
            $di->get(MonitoredUrlsApi::class),
            $di->get('NotificationAdaptersArray')
        );
    },
    CollectPingsForNotificationQueueTask::class => autowire(),
    CollectRemindersForNotificationQueueTask::class => autowire(),
    CollectUrlsForNotificationQueueTask::class => autowire(),
    SendEmailNotificationAdapter::class => autowire(),
    SlackNotificationAdapter::class => static function (ContainerInterface $di) {
        return new SlackNotificationAdapter(
            new GuzzleClientNoHttpErrors(),
            getenv('SLACK_NOTIFICATION_WEBHOOK_URL') ?: null
        );
    },
];
