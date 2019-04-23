<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use src\app\reminders\interfaces\ReminderApiInterface;

class CheckRemindersForNotificationsTask
{
    public const BATCH_NAME  = 'checkRemindersForNotifications';
    public const BATCH_TITLE = 'Check Reminders For Notifications';

    /** @var ReminderApiInterface */
    private $reminderApi;
    /** @var CheckReminderForNotification */
    private $checkReminderForNotification;

    public function __construct(
        ReminderApiInterface $reminderApi,
        CheckReminderForNotification $checkReminderForNotification
    ) {
        $this->reminderApi                  = $reminderApi;
        $this->checkReminderForNotification = $checkReminderForNotification;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke() : void
    {
        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->reminderApi->fetchAll($queryModel) as $model) {
            $this->checkReminderForNotification->check($model);
        }
    }
}
