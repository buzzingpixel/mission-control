<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\pings\PingApi;
use corbomite\queue\QueueApi;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use src\app\notifications\tasks\CheckUrlForNotificationTask;
use src\app\notifications\tasks\CheckPingForNotificationTask;
use src\app\notifications\tasks\CollectUrlsForNotificationQueueTask;
use src\app\notifications\tasks\CollectPingsForNotificationQueueTask;
use src\app\notifications\schedules\CheckUrlsForNotificationsSchedule;
use src\app\notifications\schedules\CheckPingsForNotificationsSchedule;
use src\app\notifications\notificationadapters\SlackNotificationAdapter;
use src\app\notifications\notificationadapters\SendEmailNotificationAdapter;

return [
    CheckPingsForNotificationsSchedule::class => function () {
        return new CheckPingsForNotificationsSchedule(
            Di::get(QueueApi::class)
        );
    },
    CheckUrlsForNotificationsSchedule::class => function () {
        return new CheckUrlsForNotificationsSchedule(
            Di::get(QueueApi::class)
        );
    },
    CheckPingForNotificationTask::class => function () {
        return new CheckPingForNotificationTask(
            Di::get(PingApi::class),
            [
                Di::get(SendEmailNotificationAdapter::class),
                Di::get(SlackNotificationAdapter::class),
            ]
        );
    },
    CheckUrlForNotificationTask::class => function () {
        return new CheckUrlForNotificationTask(
            Di::get(MonitoredUrlsApi::class),
            [
                Di::get(SendEmailNotificationAdapter::class),
                Di::get(SlackNotificationAdapter::class),
            ]
        );
    },
    CollectPingsForNotificationQueueTask::class => function () {
        return new CollectPingsForNotificationQueueTask(
            Di::get(PingApi::class),
            Di::get(QueueApi::class)
        );
    },
    CollectUrlsForNotificationQueueTask::class => function () {
        return new CollectUrlsForNotificationQueueTask(
            Di::get(QueueApi::class),
            Di::get(MonitoredUrlsApi::class)
        );
    },
    SendEmailNotificationAdapter::class => function () {
        return new SendEmailNotificationAdapter(
            Di::get(EmailApi::class),
            Di::get(NotificationEmailsApi::class)
        );
    },
    SlackNotificationAdapter::class => function () {
        return new SlackNotificationAdapter(
            new GuzzleClientNoHttpErrors(),
            getenv('SLACK_NOTIFICATION_WEBHOOK_URL') ?: null
        );
    },
];
