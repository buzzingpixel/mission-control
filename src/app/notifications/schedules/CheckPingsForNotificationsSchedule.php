<?php

declare(strict_types=1);

namespace src\app\notifications\schedules;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use corbomite\queue\interfaces\QueueApiInterface;
use DateTime;
use DateTimeZone;
use src\app\notifications\tasks\CheckPingsForNotificationsTask;

class CheckPingsForNotificationsSchedule
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
        $queryModel->addWhere('name', CheckPingsForNotificationsTask::BATCH_NAME);
        $queryModel->addWhere('is_finished', '0');
        $existingBatchItem = $this->queueApi->fetchOneBatch($queryModel);

        if ($existingBatchItem) {
            return;
        }

        $item = $this->queueApi->makeActionQueueItemModel();
        $item->class(CheckPingsForNotificationsTask::class);

        $batch = $this->queueApi->makeActionQueueBatchModel();
        $batch->name(CheckPingsForNotificationsTask::BATCH_NAME);
        $batch->title(CheckPingsForNotificationsTask::BATCH_TITLE);
        $batch->addItem($item);

        /** @noinspection PhpUnhandledExceptionInspection */
        $batch->assumeDeadAfter(new DateTime(
            '+20 minutes',
            new DateTimeZone('UTC')
        ));

        $this->queueApi->addToQueue($batch);
    }
}
