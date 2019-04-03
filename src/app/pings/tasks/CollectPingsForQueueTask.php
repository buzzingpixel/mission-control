<?php

declare(strict_types=1);

namespace src\app\pings\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\pings\interfaces\PingApiInterface;

class CollectPingsForQueueTask
{
    public const BATCH_NAME  = 'collectPingsForQueue';
    public const BATCH_TITLE = 'Collect Pings for Queue';

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
        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', CheckPingTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckPingTask::BATCH_NAME);
        $batch->title(CheckPingTask::BATCH_TITLE);

        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->pingApi->fetchAll($queryModel) as $model) {
            $item = $this->queueApi->makeActionQueueItemModel();

            $item->context([
                'guid' => $model->guid(),
            ]);

            $item->class(CheckPingTask::class);

            $batch->addItem($item);
        }

        $this->queueApi->addToQueue($batch);
    }
}
