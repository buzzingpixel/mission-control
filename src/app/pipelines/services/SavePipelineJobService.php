<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use DateTimeZone;
use corbomite\db\Factory as OrmFactory;
use src\app\data\PipelineJob\PipelineJob;
use src\app\data\PipelineJob\PipelineJobRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use Atlas\Table\Exception as AtlasTableException;
use src\app\data\PipelineJobItem\PipelineJobItem;
use src\app\data\PipelineJobItem\PipelineJobItemSelect;
use src\app\pipelines\events\PipelineJobAfterSaveEvent;
use src\app\pipelines\events\PipelineJobBeforeSaveEvent;
use corbomite\events\interfaces\EventDispatcherInterface;
use src\app\pipelines\exceptions\InvalidPipelineJobModel;
use src\app\pipelines\interfaces\PipelineJobModelInterface;

class SavePipelineJobService
{
    private $ormFactory;
    private $buildQuery;
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidPipelineJobModel
     */
    public function __invoke(PipelineJobModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidPipelineJobModel
     */
    public function save(PipelineJobModelInterface $model): void
    {
        $this->validate($model);

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(PipelineJob::class, $fetchModel)
            ->with([
                'pipeline_job_items' => function (PipelineJobItemSelect $select) {
                    $select->orderBy('`order` ASC');
                },
            ])
            ->fetchRecord();

        if (! $existingRecord) {
            $this->eventDispatcher->dispatch(new PipelineJobBeforeSaveEvent($model, true));

            $this->saveNew($model);

            $this->eventDispatcher->dispatch(new PipelineJobAfterSaveEvent($model, true));

            return;
        }

        $this->eventDispatcher->dispatch(new PipelineJobBeforeSaveEvent($model));

        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new PipelineJobAfterSaveEvent($model));
    }

    /**
     * @throws InvalidPipelineJobModel
     */
    private function validate(PipelineJobModelInterface $model): void
    {
        if (! $model->pipelineGuid()) {
            throw new InvalidPipelineJobModel();
        }

        foreach ($model->pipelineJobItems() as $item) {
            if (! $item->pipelineItemGuid()) {
                throw new InvalidPipelineJobModel();
            }
        }
    }

    private function saveNew(PipelineJobModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(PipelineJob::class);

        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        PipelineJobModelInterface $model,
        PipelineJobRecord $record
    ): void {
        $orm = $this->ormFactory->makeOrm();

        $items = $orm->newRecordSet(PipelineJobItem::class);

        if ($record->pipeline_job_items) {
            $items = $record->pipeline_job_items;
        }

        $order = 0;

        foreach ($model->pipelineJobItems() as $item) {
            $order++;

            $itemRecord = $items->getOneBy([
                'guid' => $item->getGuidAsBytes(),
            ]);

            $propArray = [
                'guid' => $item->getGuidAsBytes(),
                'pipeline_guid' => $model->getPipelineGuidAsBytes(),
                'pipeline_job_guid' => $model->getGuidAsBytes(),
                'pipeline_item_guid' => $item->getPipelineItemGuidAsBytes(),
                'order' => $order,
                'has_failed' => $item->hasFailed(),
                'log_content' => $item->logContent(),
                'finished_at' => null,
                'finished_at_time_zone' => null,
            ];

            if ($finishedAt = $item->finishedAt()) {
                $finishedAt->setTimezone(new DateTimeZone('UTC'));
                $propArray['finished_at'] = $finishedAt->format('Y-m-d H:i:s');
                $propArray['finished_at_time_zone'] = $finishedAt->getTimezone()->getName();
            }

            if ($itemRecord) {
                $itemRecord->set($propArray);
                continue;
            }

            $items->appendNew($propArray);
        }

        $jobAddedAt = $model->jobAddedAt();

        $jobAddedAt->setTimezone(new DateTimeZone('UTC'));

        $record->pipeline_guid = $model->getPipelineGuidAsBytes();
        $record->has_started = $model->hasStarted();
        $record->is_finished = $model->isFinished();
        $record->has_failed = $model->hasFailed();
        $record->percent_complete = $model->percentComplete();
        $record->job_added_at = $jobAddedAt->format('Y-m-d H:i:s');
        $record->job_added_at_time_zone = $jobAddedAt->getTimezone()->getName();
        $record->job_finished_at = null;
        $record->job_finished_at_zone = null;

        if ($jobFinishedAt = $model->jobFinishedAt()) {
            $jobFinishedAt->setTimezone(new DateTimeZone('UTC'));
            $record->job_finished_at = $jobFinishedAt->format('Y-m-d H:i:s');
            $record->job_finished_at_zone = $jobFinishedAt->getTimezone()->getName();
        }

        try {
            $orm->persist($record);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() !== 'Expected 1 row affected, actual 0.') {
                throw $e;
            }
        }

        try {
            $orm->persistRecordSet($items);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() !== 'Expected 1 row affected, actual 0.') {
                throw $e;
            }
        }
    }
}
