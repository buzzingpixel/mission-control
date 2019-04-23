<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use DateTime;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\reminders\interfaces\ReminderModelInterface;
use Throwable;
use function getenv;
use function time;

class CheckReminderForNotification
{
    /** @var EmailApiInterface */
    private $emailApi;
    /** @var ReminderApiInterface */
    private $reminderApi;
    /** @var SendNotificationAdapterInterface[] */
    private $sendNotificationAdapters;

    /**
     * @param SendNotificationAdapterInterface[] $sendNotificationAdapters
     */
    public function __construct(
        EmailApiInterface $emailApi,
        ReminderApiInterface $reminderApi,
        array $sendNotificationAdapters = []
    ) {
        $this->emailApi                 = $emailApi;
        $this->reminderApi              = $reminderApi;
        $this->sendNotificationAdapters = $sendNotificationAdapters;
    }

    public function check(ReminderModelInterface $model) : void
    {
        try {
            $this->innerCheck($model);
        } catch (Throwable $e) {
            // if (getenv('DEV_MODE') === 'true') {
            //     /** @noinspection PhpUnhandledExceptionInspection */
            //     throw $e;
            // }

            $this->sendErrorEmail($e);
        }
    }

    private function sendErrorEmail(Throwable $e) : void
    {
        try {
            $emailModel = $this->emailApi->createEmailModel();
            $emailModel->toEmail(getenv('WEBMASTER_EMAIL_ADDRESS'));
            $emailModel->subject('An exception was thrown checking a Reminder for notification');
            $emailModel->messagePlainText(
                'While checking a Reminder for notification ' .
                "an exception was thrown.\n\n" .
                'File: ' . $e->getFile() . "\n" .
                'Line: ' . $e->getLine() . "\n" .
                'Message: ' . $e->getMessage()
            );

            $this->emailApi->sendEmail($emailModel);
        } catch (Throwable $e) {
        }
    }

    public function innerCheck(ReminderModelInterface $model) : void
    {
        // Sanity check
        if (! $model->isActive()) {
            return;
        }

        $currentTimestamp = time();

        $startRemindingTimestamp = $model->startRemindingOn()->getTimestamp();

        // If it's not time to start reminding yet, we can stop here
        if ($currentTimestamp < $startRemindingTimestamp) {
            return;
        }

        /**
         * We only want to send reminders every 24 hours. While our schedule
         * should take care of this by only initiating at midnight, we'll do a
         * little sanity checking here. We'll do 22 hours
         */

        // We'll do 22 hours for safety

        $lastReminderSentTimestampPlus22 = 0;

        $last = $model->lastReminderSent();

        if ($last) {
            $twentyTwoHoursInSeconds         = 79200;
            $lastReminderSentTimestampPlus22 = $last->getTimestamp() + $twentyTwoHoursInSeconds;
        }

        if ($currentTimestamp < $lastReminderSentTimestampPlus22) {
            return;
        }

        $reminderUrl = getenv('SITE_URL') . '/reminders/view/' . $model->slug();

        $sbj = 'The reminder ' . $model->title() . ' is due';
        $msg = '';

        $tmpMsg = $model->message();

        if ($tmpMsg) {
            $msg .= $tmpMsg . "\n";
        }

        $msg .= 'Reminder started on ' . $model->startRemindingOn()->format('Y-m-d') . "\n";

        $msg .= 'View reminder: ' . $reminderUrl;

        foreach ($this->sendNotificationAdapters as $adapter) {
            $adapter->send($sbj, $msg, [
                'urls' => [[
                    'content' => 'View Reminder',
                    'href' => $reminderUrl,
                ],
                ],
            ]);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $model->lastReminderSent(new DateTime());

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->reminderApi->save($model);
    }
}
