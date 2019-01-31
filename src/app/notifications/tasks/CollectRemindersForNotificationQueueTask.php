<?php
declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\interfaces\QueueApiInterface;
use src\app\reminders\interfaces\ReminderApiInterface;
use corbomite\queue\exceptions\InvalidActionQueueBatchModel;

class CollectRemindersForNotificationQueueTask
{
    public const BATCH_NAME = 'collectRemindersForNotificationQueue';
    public const BATCH_TITLE = 'Collect Reminders for Notification Queue';

    private $queueApi;
    private $reminderApi;

    public function __construct(
        QueueApiInterface $queueApi,
        ReminderApiInterface $reminderApi
    ) {
        $this->queueApi = $queueApi;
        $this->reminderApi = $reminderApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke(): void
    {
        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', CheckReminderForNotificationTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckReminderForNotificationTask::BATCH_NAME);
        $batch->title(CheckReminderForNotificationTask::BATCH_TITLE);

        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->reminderApi->fetchAll($queryModel) as $model) {
            $item = $this->queueApi->makeActionQueueItemModel();

            $item->context([
                'guid' => $model->guid(),
            ]);

            $item->class(CheckReminderForNotificationTask::class);

            $batch->addItem($item);
        }

        $this->queueApi->addToQueue($batch);
    }
}
