<?php

declare(strict_types=1);

namespace src\app\monitoredurls\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class CollectUrlsForQueueTask
{
    public const BATCH_NAME  = 'collectUrlsForQueue';
    public const BATCH_TITLE = 'Collect URLs for Queue';

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
            $this->processUrl($model);
        }
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    private function processUrl(MonitoredUrlModelInterface $model) : void
    {
        $batchName = 'check_url_' . $model->guid();

        $batchTitle = 'Check URL: ' . $model->title();

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

        $item->class(CheckUrlTask::class);

        $batch->addItem($item);

        $this->queueApi->addToQueue($batch);
    }
}
