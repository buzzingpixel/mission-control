<?php
declare(strict_types=1);

namespace src\app\notifications\schedules;

use corbomite\queue\interfaces\QueueApiInterface;
use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use src\app\notifications\tasks\CheckReminderForNotificationTask;
use src\app\notifications\tasks\CollectRemindersForNotificationQueueTask;

class CheckRemindersForNotificationsSchedule
{
    private $queueApi;

    public function __construct(QueueApiInterface $queueApi)
    {
        $this->queueApi = $queueApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke(): void
    {
        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', CollectRemindersForNotificationQueueTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $queryModel->addWhere('name', CheckReminderForNotificationTask::BATCH_NAME, '=', true);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CollectRemindersForNotificationQueueTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CollectRemindersForNotificationQueueTask::BATCH_NAME);
        $batch->title(CollectRemindersForNotificationQueueTask::BATCH_TITLE);
        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
