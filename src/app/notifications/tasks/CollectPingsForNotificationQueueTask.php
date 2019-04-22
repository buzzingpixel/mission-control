<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\pings\interfaces\PingApiInterface;
use src\app\pings\interfaces\PingModelInterface;

class CollectPingsForNotificationQueueTask
{
    public const BATCH_NAME  = 'collectPingsForNotificationQueue';
    public const BATCH_TITLE = 'Collect Pings for Notification Queue';

    /** @var PingApiInterface */
    private $pingApi;
    /** @var QueueApiInterface */
    private $queueApi;

    public function __construct(
        PingApiInterface $pingApi,
        QueueApiInterface $queueApi
    ) {
        $this->pingApi  = $pingApi;
        $this->queueApi = $queueApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke() : void
    {
        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->pingApi->fetchAll($queryModel) as $model) {
            $this->processItem($model);
        }
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    private function processItem(PingModelInterface $model) : void
    {
        $batchName = 'check_ping_for_notification_' . $model->guid();

        $batchTitle = 'Check Ping For Notification: ' . $model->title();

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

        $item->class(CheckPingForNotificationTask::class);

        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
