<?php

declare(strict_types=1);

namespace src\app\notifications\schedules;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\notifications\tasks\CheckUrlsForNotificationsTask;

class CheckUrlsForNotificationsSchedule
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
        $queryModel->addWhere('name', CheckUrlsForNotificationsTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CheckUrlsForNotificationsTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckUrlsForNotificationsTask::BATCH_NAME);
        $batch->title(CheckUrlsForNotificationsTask::BATCH_TITLE);
        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
