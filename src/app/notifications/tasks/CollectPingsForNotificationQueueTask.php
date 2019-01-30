<?php
declare(strict_types=1);

namespace src\app\notifications\tasks;

use src\app\pings\interfaces\PingApiInterface;
use corbomite\queue\interfaces\QueueApiInterface;
use corbomite\queue\exceptions\InvalidActionQueueBatchModel;

class CollectPingsForNotificationQueueTask
{
    public const BATCH_NAME = 'collectPingsForNotificationQueue';
    public const BATCH_TITLE = 'Collect Pings for Notification Queue';

    private $pingApi;
    private $queueApi;

    public function __construct(
        PingApiInterface $pingApi,
        QueueApiInterface $queueApi
    ) {
        $this->pingApi = $pingApi;
        $this->queueApi = $queueApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke(): void
    {
        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', CheckPingForNotificationTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckUrlForNotificationTask::BATCH_NAME);
        $batch->title(CheckUrlForNotificationTask::BATCH_TITLE);

        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->pingApi->fetchAll($queryModel) as $model) {
            $item = $this->queueApi->makeActionQueueItemModel();

            $item->context([
                'guid' => $model->guid(),
            ]);

            $item->class(CheckPingForNotificationTask::class);

            $batch->addItem($item);
        }

        $this->queueApi->addToQueue($batch);
    }
}
