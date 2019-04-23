<?php

declare(strict_types=1);

namespace src\app\monitoredurls\schedules;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use DateTime;
use DateTimeZone;
use src\app\monitoredurls\tasks\CheckUrlsTask;

class CheckUrlsSchedule
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
        $queryModel->addWhere('name', CheckUrlsTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CheckUrlsTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckUrlsTask::BATCH_NAME);
        $batch->title(CheckUrlsTask::BATCH_TITLE);
        $batch->addItem($item);

        /** @noinspection PhpUnhandledExceptionInspection */
        $batch->assumeDeadAfter(new DateTime(
            '+20 minutes',
            new DateTimeZone('UTC')
        ));

        $this->queueApi->addToQueue($batch);
    }
}
