<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use DateTime;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\reminders\interfaces\ReminderModelInterface;
use function getenv;
use function time;

class CheckReminderForNotificationTask
{
    /** @var ReminderApiInterface */
    private $reminderApi;

    /** @var SendNotificationAdapterInterface[] */
    private $sendNotificationAdapters;

    /**
     * @param SendNotificationAdapterInterface[] $sendNotificationAdapters
     */
    public function __construct(
        ReminderApiInterface $reminderApi,
        array $sendNotificationAdapters = []
    ) {
        $this->reminderApi              = $reminderApi;
        $this->sendNotificationAdapters = $sendNotificationAdapters;
    }

    /**
     * @param mixed[] $context
     */
    public function __invoke(array $context) : void
    {
        $model = $this->getModel($context['guid']);

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

    private function getModel(string $guid) : ReminderModelInterface
    {
        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addWhere('guid', $this->reminderApi->uuidToBytes($guid));

        return $this->reminderApi->fetchOne($queryModel);
    }
}
