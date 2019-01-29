<?php
declare(strict_types=1);

use corbomite\di\Di;
use corbomite\queue\QueueApi;
use buzzingpixel\corbomitemailer\EmailApi;
use src\app\monitoredurls\MonitoredUrlsApi;
use src\app\notificationemails\NotificationEmailsApi;
use src\app\notifications\tasks\CheckUrlForNotificationTask;
use src\app\notifications\tasks\CollectUrlsForNotificationQueueTask;
use src\app\notifications\schedules\CheckUrlsForNotificationsSchedule;
use src\app\notifications\notificationadapters\SendEmailNotificationAdapter;

return [
    CheckUrlsForNotificationsSchedule::class => function () {
        return new CheckUrlsForNotificationsSchedule(
            Di::get(QueueApi::class)
        );
    },
    CheckUrlForNotificationTask::class => function () {
        return new CheckUrlForNotificationTask(
            Di::get(MonitoredUrlsApi::class),
            [
                Di::get(SendEmailNotificationAdapter::class)
            ]
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
];
