<?php

declare(strict_types=1);

namespace src\app\pipelines\services;

use Atlas\Table\Exception as AtlasTableException;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\interfaces\EventDispatcherInterface;
use DateTimeZone;
use src\app\data\PipelineJob\PipelineJob;
use src\app\data\PipelineJob\PipelineJobRecord;
use src\app\data\PipelineJobItem\PipelineJobItem;
use src\app\data\PipelineJobItem\PipelineJobItemSelect;
use src\app\pipelines\events\PipelineJobAfterSaveEvent;
use src\app\pipelines\events\PipelineJobBeforeSaveEvent;
use src\app\pipelines\exceptions\InvalidPipelineJobModel;
use src\app\pipelines\interfaces\PipelineJobModelInterface;

class SavePipelineJobService
{
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidPipelineJobModel
     */
    public function __invoke(PipelineJobModelInterface $model) : void
    {
        $this->save($model);
    }

    /**
     * @throws InvalidPipelineJobModel
     */
    public function save(PipelineJobModelInterface $model) : void
    {
        $this->validate($model);

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        /** @noinspection PhpUnhandledExceptionInspection */
        $existingRecord = $this->buildQuery->build(PipelineJob::class, $fetchModel)
            ->with([
                'pipeline_job_items' => static function (PipelineJobItemSelect $select) : void {
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

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpParamsInspection */
        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new PipelineJobAfterSaveEvent($model));
    }

    /**
     * @throws InvalidPipelineJobModel
     */
    private function validate(PipelineJobModelInterface $model) : void
    {
        if (! $model->pipeline()) {
            throw new InvalidPipelineJobModel();
        }

        foreach ($model->pipelineJobItems() as $item) {
            if (! $item->pipelineItem()) {
                throw new InvalidPipelineJobModel();
            }
        }
    }

    private function saveNew(PipelineJobModelInterface $model) : void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(PipelineJob::class);

        $record->guid = $model->getGuidAsBytes();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->finalSave($model, $record);
    }

    private function finalSave(
        PipelineJobModelInterface $model,
        PipelineJobRecord $record
    ) : void {
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
                'pipeline_guid' => $model->pipeline()->getGuidAsBytes(),
                'pipeline_job_guid' => $model->getGuidAsBytes(),
                'pipeline_item_guid' => $item->pipelineItem()->getGuidAsBytes(),
                'order' => $order,
                'has_failed' => $item->hasFailed(),
                'log_content' => $item->logContent(),
                'finished_at' => null,
                'finished_at_time_zone' => null,
            ];

            $finishedAt = $item->finishedAt();

            if ($finishedAt) {
                $finishedAt->setTimezone(new DateTimeZone('UTC'));
                $propArray['finished_at']           = $finishedAt->format('Y-m-d H:i:s');
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

        $record->pipeline_guid          = $model->pipeline()->getGuidAsBytes();
        $record->has_started            = $model->hasStarted();
        $record->is_finished            = $model->isFinished();
        $record->has_failed             = $model->hasFailed();
        $record->percent_complete       = $model->percentComplete();
        $record->job_added_at           = $jobAddedAt->format('Y-m-d H:i:s');
        $record->job_added_at_time_zone = $jobAddedAt->getTimezone()->getName();
        $record->job_finished_at        = null;
        $record->job_finished_at_zone   = null;

        $jobFinishedAt = $model->jobFinishedAt();

        if ($jobFinishedAt) {
            $jobFinishedAt->setTimezone(new DateTimeZone('UTC'));
            $record->job_finished_at      = $jobFinishedAt->format('Y-m-d H:i:s');
            $record->job_finished_at_zone = $jobFinishedAt->getTimezone()->getName();
        }

        try {
            $orm->persist($record);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() !== 'Expected 1 row affected, actual 0.') {
                /** @noinspection PhpUnhandledExceptionInspection */
                throw $e;
            }
        }

        try {
            $orm->persistRecordSet($items);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() !== 'Expected 1 row affected, actual 0.') {
                /** @noinspection PhpUnhandledExceptionInspection */
                throw $e;
            }
        }
    }
}
