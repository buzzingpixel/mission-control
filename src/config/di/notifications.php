<?php
declare(strict_types=1);

use src\app\pings\PingApi;
use corbomite\queue\QueueApi;
use src\app\reminders\ReminderApi;
use Psr\Container\ContainerInterface;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use src\app\notifications\tasks\CheckUrlForNotificationTask;
use src\app\notifications\tasks\CheckPingForNotificationTask;
use src\app\notifications\tasks\CheckReminderForNotificationTask;
use src\app\notifications\tasks\CollectUrlsForNotificationQueueTask;
use src\app\notifications\tasks\CollectPingsForNotificationQueueTask;
use src\app\notifications\schedules\CheckUrlsForNotificationsSchedule;
use src\app\notifications\schedules\CheckPingsForNotificationsSchedule;
use src\app\notifications\notificationadapters\SlackNotificationAdapter;
use src\app\notifications\tasks\CollectRemindersForNotificationQueueTask;
use src\app\notifications\schedules\CheckRemindersForNotificationsSchedule;
use src\app\notifications\notificationadapters\SendEmailNotificationAdapter;

return [
    'NotificationAdaptersArray' => static function (ContainerInterface $di) {
        return [
            $di->get(SendEmailNotificationAdapter::class),
            $di->get(SlackNotificationAdapter::class),
        ];
    },
    CheckPingsForNotificationsSchedule::class => static function (ContainerInterface $di) {
        return new CheckPingsForNotificationsSchedule(
            $di->get(QueueApi::class)
        );
    },
    CheckRemindersForNotificationsSchedule::class => static function (ContainerInterface $di) {
        return new CheckRemindersForNotificationsSchedule(
            $di->get(QueueApi::class)
        );
    },
    CheckUrlsForNotificationsSchedule::class => static function (ContainerInterface $di) {
        return new CheckUrlsForNotificationsSchedule(
            $di->get(QueueApi::class)
        );
    },
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
    CollectPingsForNotificationQueueTask::class => static function (ContainerInterface $di) {
        return new CollectPingsForNotificationQueueTask(
            $di->get(PingApi::class),
            $di->get(QueueApi::class)
        );
    },
    CollectRemindersForNotificationQueueTask::class => static function (ContainerInterface $di) {
        return new CollectRemindersForNotificationQueueTask(
            $di->get(QueueApi::class),
            $di->get(ReminderApi::class)
        );
    },
    CollectUrlsForNotificationQueueTask::class => static function (ContainerInterface $di) {
        return new CollectUrlsForNotificationQueueTask(
            $di->get(QueueApi::class),
            $di->get(MonitoredUrlsApi::class)
        );
    },
    SendEmailNotificationAdapter::class => static function (ContainerInterface $di) {
        return new SendEmailNotificationAdapter(
            $di->get(EmailApi::class),
            $di->get(NotificationEmailsApi::class)
        );
    },
    SlackNotificationAdapter::class => static function (ContainerInterface $di) {
        return new SlackNotificationAdapter(
            new GuzzleClientNoHttpErrors(),
            getenv('SLACK_NOTIFICATION_WEBHOOK_URL') ?: null
        );
    },
];
