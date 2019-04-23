<?php

declare(strict_types=1);

namespace src\app\notifications\schedules;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\notifications\tasks\CheckRemindersForNotificationsTask;

class CheckRemindersForNotificationsSchedule
{
    /** @var QueueApiInterface */
    private $queueApi;

    public function __construct(QueueApiInterface $queueApi)
    {
        $this->queueApi = $queueApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke() : void
    {
        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', CheckRemindersForNotificationsTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CheckRemindersForNotificationsTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckRemindersForNotificationsTask::BATCH_NAME);
        $batch->title(CheckRemindersForNotificationsTask::BATCH_TITLE);
        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
