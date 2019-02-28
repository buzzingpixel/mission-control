<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use Cocur\Slugify\Slugify;
use src\app\data\Pipeline\Pipeline;
use Ramsey\Uuid\UuidFactoryInterface;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Pipeline\PipelineRecord;
use src\app\data\PipelineItem\PipelineItem;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\PipelineItem\PipelineItemSelect;
use Atlas\Table\Exception as AtlasTableException;
use src\app\pipelines\events\PipelineAfterSaveEvent;
use src\app\pipelines\events\PipelineBeforeSaveEvent;
use src\app\pipelines\exceptions\InvalidPipelineModel;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\servers\exceptions\TitleNotUniqueException;
use corbomite\events\interfaces\EventDispatcherInterface;

class SavePipelineService
{
    private $slugify;
    private $ormFactory;
    private $buildQuery;
    private $uuidFactory;
    private $eventDispatcher;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        UuidFactoryInterface $uuidFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->uuidFactory = $uuidFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidPipelineModel
     * @throws TitleNotUniqueException
     */
    public function __invoke(PipelineModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidPipelineModel
     * @throws TitleNotUniqueException
     */
    public function save(PipelineModelInterface $model): void
    {
        $this->validate($model);

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(Pipeline::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new TitleNotUniqueException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(Pipeline::class, $fetchModel)
            ->with([
                'pipeline_items' => function (PipelineItemSelect $select) {
                    $select->orderBy('`order` ASC');
                },
            ])
            ->fetchRecord();

        if (! $existingRecord) {
            $this->eventDispatcher->dispatch(new PipelineBeforeSaveEvent($model, true));

            $this->saveNew($model);

            $this->eventDispatcher->dispatch(new PipelineAfterSaveEvent($model, true));

            return;
        }

        $this->eventDispatcher->dispatch(new PipelineBeforeSaveEvent($model));

        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new PipelineAfterSaveEvent($model));
    }

    /**
     * @throws InvalidPipelineModel
     */
    private function validate(PipelineModelInterface $model): void
    {
        if (! $model->title()) {
            throw new InvalidPipelineModel();
        }

        foreach ($model->pipelineItems() as $item) {
            if (! $item->script()) {
                throw new InvalidPipelineModel();
            }
        }
    }

    private function saveNew(PipelineModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Pipeline::class);

        $record->guid = $model->getGuidAsBytes();

        $record->secret_id = $this->uuidFactory->uuid4()->toString();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        PipelineModelInterface $model,
        PipelineRecord $record
    ): void {
        $orm = $this->ormFactory->makeOrm();

        $items = $orm->newRecordSet(PipelineItem::class);

        if ($record->pipeline_items) {
            $items = $record->pipeline_items;
        }

        $order = 0;

        foreach ($model->pipelineItems() as $item) {
            $order++;

            $itemRecord = $items->getOneBy([
                'guid' => $item->getGuidAsBytes(),
            ]);

            if ($itemRecord) {
                $itemRecord->order = $order;
                $itemRecord->script = $item->script();
                continue;
            }

            $items->appendNew([
                'guid' => $item->getGuidAsBytes(),
                'pipeline_guid' => $model->getGuidAsBytes(),
                'order' => $order,
                'script' => $item->script(),
            ]);
        }

        $record->project_guid = $model->getProjectGuidAsBytes();
        $record->is_active = $model->isActive();
        $record->title = $model->title();
        $record->slug = $model->slug();
        $record->description = $model->description();
        $record->pipeline_items = $items;

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
