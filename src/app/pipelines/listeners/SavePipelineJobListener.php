<?php
declare(strict_types=1);

namespace src\app\pipelines\listeners;

use src\app\pipelines\tasks\RunJobItemTask;
use corbomite\events\interfaces\EventInterface;
use corbomite\queue\interfaces\QueueApiInterface;
use src\app\pipelines\events\PipelineJobAfterSaveEvent;
use corbomite\events\interfaces\EventListenerInterface;

class SavePipelineJobListener implements EventListenerInterface
{
    private $queueApi;

    public function __construct(QueueApiInterface $queueApi)
    {
        $this->queueApi = $queueApi;
    }

    public function call(EventInterface $event)
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

        $this->queueApi->addToQueue($batch);
    }
}
