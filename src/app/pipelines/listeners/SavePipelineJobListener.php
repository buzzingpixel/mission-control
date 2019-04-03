<?php

declare(strict_types=1);

namespace src\app\pipelines\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\pipelines\events\PipelineJobAfterSaveEvent;
use src\app\pipelines\tasks\RunJobItemTask;

class SavePipelineJobListener implements EventListenerInterface
{
    /** @var QueueApiInterface */
    private $queueApi;

    public function __construct(QueueApiInterface $queueApi)
    {
        $this->queueApi = $queueApi;
    }

    public function call(EventInterface $event) : void
    {
        /** @var PipelineJobAfterSaveEvent $event */

        if (! $event->new()) {
            return;
        }

        $job = $event->model();

        $batch = $this->queueApi->makeActionQueueBatchModel();

        $batch->name($job->pipeline()->slug());

        $batch->title($job->pipeline()->title());

        foreach ($job->pipelineJobItems() as $jobItem) {
            $item = $this->queueApi->makeActionQueueItemModel();

            $item->class(RunJobItemTask::class);

            $item->context([
                'jobItemGuid' => $jobItem->guid(),
            ]);

            $batch->addItem($item);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->queueApi->addToQueue($batch);
    }
}
