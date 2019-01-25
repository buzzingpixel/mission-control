<?php
declare(strict_types=1);

namespace src\app\monitoredurls\schedules;

use src\app\monitoredurls\tasks\CheckUrlTask;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\monitoredurls\tasks\CollectUrlsForQueueTask;
use corbomite\queue\exceptions\InvalidActionQueueBatchModel;

class CheckUrlsSchedule
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
        $queryModel->addWhere('name', CollectUrlsForQueueTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $queryModel->addWhere('name', CheckUrlTask::BATCH_NAME, '=', true);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CollectUrlsForQueueTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CollectUrlsForQueueTask::BATCH_NAME);
        $batch->title(CollectUrlsForQueueTask::BATCH_TITLE);
        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}