<?php
declare(strict_types=1);

namespace src\app\pings\schedules;

use src\app\pings\tasks\CheckPingTask;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\pings\tasks\CollectPingsForQueueTask;
use corbomite\queue\exceptions\InvalidActionQueueBatchModel;

class CheckPingsSchedule
{
    private $queueApi;

    public function __construct(QueueApiInterface $queueApi)
    {
        $this->queueApi = $queueApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke()
    {
        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', CollectPingsForQueueTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $queryModel->addWhere('name', CheckPingTask::BATCH_NAME, '=', true);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CollectPingsForQueueTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CollectPingsForQueueTask::BATCH_NAME);
        $batch->title(CollectPingsForQueueTask::BATCH_TITLE);
        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
