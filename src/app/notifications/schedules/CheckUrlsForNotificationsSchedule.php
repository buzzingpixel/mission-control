<?php
declare(strict_types=1);

namespace src\app\notifications\schedules;

use corbomite\queue\interfaces\QueueApiInterface;
use src\app\notifications\tasks\CheckUrlForNotificationTask;
use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use src\app\notifications\tasks\CollectUrlsForNotificationQueueTask;

class CheckUrlsForNotificationsSchedule
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
        $queryModel->addWhere('name', CollectUrlsForNotificationQueueTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $queryModel->addWhere('name', CheckUrlForNotificationTask::BATCH_NAME, '=', true);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CollectUrlsForNotificationQueueTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CollectUrlsForNotificationQueueTask::BATCH_NAME);
        $batch->title(CollectUrlsForNotificationQueueTask::BATCH_TITLE);
        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
