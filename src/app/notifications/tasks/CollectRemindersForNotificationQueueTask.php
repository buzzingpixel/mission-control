<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\reminders\interfaces\ReminderModelInterface;

class CollectRemindersForNotificationQueueTask
{
    public const BATCH_NAME  = 'collectRemindersForNotificationQueue';
    public const BATCH_TITLE = 'Collect Reminders for Notification Queue';

    /** @var QueueApiInterface */
    private $queueApi;
    /** @var ReminderApiInterface */
    private $reminderApi;

    public function __construct(
        QueueApiInterface $queueApi,
        ReminderApiInterface $reminderApi
    ) {
        $this->queueApi    = $queueApi;
        $this->reminderApi = $reminderApi;
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
            $this->processItem($model);
        }
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    private function processItem(ReminderModelInterface $model) : void
    {
        $batchName = 'check_reminder_for_notification_' . $model->guid();

        $batchTitle = 'Check Reminder For Notification: ' . $model->title();

        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', $batchName);
        $queryModel->addWhere('is_finished', '0');

        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $batch = $this->queueApi->makeActionQueueBatchModel();

        $batch->name($batchName);

        $batch->title($batchTitle);

        $item = $this->queueApi->makeActionQueueItemModel();

        $item->context([
            'guid' => $model->guid(),
        ]);

        $item->class(CheckReminderForNotificationTask::class);

        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
