<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class CollectUrlsForNotificationQueueTask
{
    public const BATCH_NAME  = 'collectUrlsForNotificationQueue';
    public const BATCH_TITLE = 'Collect URLs for Notification Queue';

    /** @var QueueApiInterface */
    private $queueApi;
    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;

    public function __construct(
        QueueApiInterface $queueApi,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->queueApi         = $queueApi;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke() : void
    {
        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->monitoredUrlsApi->fetchAll($queryModel) as $model) {
            $this->processItem($model);
        }
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    private function processItem(MonitoredUrlModelInterface $model) : void
    {
        $batchName = 'check_url_for_notification_' . $model->guid();

        $batchTitle = 'Check URL For Notification: ' . $model->title();

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

        $item->class(CheckUrlForNotificationTask::class);

        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
