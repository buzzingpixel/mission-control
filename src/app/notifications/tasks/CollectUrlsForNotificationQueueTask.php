<?php
declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\interfaces\QueueApiInterface;
use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class CollectUrlsForNotificationQueueTask
{
    public const BATCH_NAME = 'collectUrlsForNotificationQueue';
    public const BATCH_TITLE = 'Collect URLs for Notification Queue';

    private $queueApi;
    private $monitoredUrlsApi;

    public function __construct(
        QueueApiInterface $queueApi,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->queueApi = $queueApi;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke(): void
    {
        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addWhere('name', CheckUrlForNotificationTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckUrlForNotificationTask::BATCH_NAME);
        $batch->title(CheckUrlForNotificationTask::BATCH_TITLE);

        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->monitoredUrlsApi->fetchAll($queryModel) as $model) {
            $item = $this->queueApi->makeActionQueueItemModel();

            $item->context([
                'guid' => $model->guid(),
            ]);

            $item->class(CheckUrlForNotificationTask::class);

            $batch->addItem($item);
        }

        $this->queueApi->addToQueue($batch);
    }
}
