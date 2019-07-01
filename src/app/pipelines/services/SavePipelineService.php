<?php

declare(strict_types=1);

namespace src\app\pipelines\services;

use Atlas\Pdo\Connection;
use Atlas\Table\Exception as AtlasTableException;
use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\interfaces\EventDispatcherInterface;
use Ramsey\Uuid\UuidFactoryInterface;
use src\app\data\Pipeline\Pipeline;
use src\app\data\Pipeline\PipelineRecord;
use src\app\data\PipelineItem\PipelineItem;
use src\app\data\PipelineItem\PipelineItemSelect;
use src\app\pipelines\events\PipelineAfterSaveEvent;
use src\app\pipelines\events\PipelineBeforeSaveEvent;
use src\app\pipelines\exceptions\InvalidPipelineModel;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\servers\exceptions\TitleNotUniqueException;
use src\app\servers\interfaces\ServerModelInterface;
use function array_walk;

class SavePipelineService
{
    /** @var Slugify */
    private $slugify;
    /** @var Connection */
    private $connection;
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var UuidFactoryInterface */
    private $uuidFactory;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        Slugify $slugify,
        Connection $connection,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        UuidFactoryInterface $uuidFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->slugify         = $slugify;
        $this->connection      = $connection;
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->uuidFactory     = $uuidFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidPipelineModel
     * @throws TitleNotUniqueException
     */
    public function __invoke(PipelineModelInterface $model) : void
    {
        $this->save($model);
    }

    /**
     * @throws InvalidPipelineModel
     * @throws TitleNotUniqueException
     */
    public function save(PipelineModelInterface $model) : void
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
        /** @noinspection PhpUnhandledExceptionInspection */
        $existingRecord = $this->buildQuery->build(Pipeline::class, $fetchModel)
            ->with([
                'pipeline_items' => static function (PipelineItemSelect $select) : void {
                    $select->orderBy('`order` ASC');

                    $select->with(['servers']);
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

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpParamsInspection */
        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new PipelineAfterSaveEvent($model));
    }

    /**
     * @throws InvalidPipelineModel
     */
    private function validate(PipelineModelInterface $model) : void
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

    private function saveNew(PipelineModelInterface $model) : void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Pipeline::class);

        $record->guid = $model->getGuidAsBytes();

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->secret_id = $this->uuidFactory->uuid4()->toString();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->finalSave($model, $record);
    }

    private function finalSave(
        PipelineModelInterface $model,
        PipelineRecord $record
    ) : void {
        $orm = $this->ormFactory->makeOrm();

        $items = $orm->newRecordSet(PipelineItem::class);

        if ($record->pipeline_items) {
            $items = $record->pipeline_items;
        }

        $pipelineItemsToDelete = [];

        $itemsQuery = $this->connection->prepare(
            'SELECT `guid` FROM `pipeline_items` WHERE `pipeline_guid` = :pipeline_guid'
        );

        $itemsQuery->execute([
            ':pipeline_guid' => $model->getGuidAsBytes(),
        ]);

        foreach ($itemsQuery->fetchAll() as $queryItem) {
            $pipelineItemsToDelete[$queryItem['guid']] = $queryItem['guid'];
        }

        $order = 0;

        foreach ($model->pipelineItems() as $item) {
            unset($pipelineItemsToDelete[$item->getGuidAsBytes()]);

            $order++;

            $itemRecord = $items->getOneBy([
                'guid' => $item->getGuidAsBytes(),
            ]);

            $deleteServersQuery = $this->connection->prepare(
                'DELETE FROM `pipeline_item_servers` WHERE `pipeline_item_guid` = :pipeline_item_guid'
            );

            $deleteServersQuery->execute([
                ':pipeline_item_guid' => $item->getGuidAsBytes(),
            ]);

            $insertServersQuery = $this->connection->prepare(
                'INSERT INTO `pipeline_item_servers` ' .
                    '(pipeline_item_guid, server_guid) ' .
                    'VALUES (:pipeline_item_guid, :server_guid)'
            );

            $servers = $item->servers();

            array_walk($servers, static function (ServerModelInterface $server) use (
                $insertServersQuery,
                $item
            ) : void {
                $insertServersQuery->execute([
                    ':pipeline_item_guid' => $item->getGuidAsBytes(),
                    ':server_guid' => $server->getGuidAsBytes(),
                ]);
            });

            $propArray = [
                'guid' => $item->getGuidAsBytes(),
                'pipeline_guid' => $model->getGuidAsBytes(),
                'order' => $order,
                'description' => $item->description(),
                'script' => $item->script(),
                'run_after_fail' => $item->runAfterFail(),
            ];

            if ($itemRecord) {
                $itemRecord->set($propArray);
                continue;
            }

            $items->appendNew($propArray);
        }

        if ($pipelineItemsToDelete) {
            foreach ($pipelineItemsToDelete as $guid) {
                $deleteItemQuery = $this->connection->prepare(
                    'DELETE FROM `pipeline_items` WHERE `guid` = :guid'
                );

                $deleteItemQuery->execute([':guid' => $guid]);
            }
        }

        $record->project_guid          = $model->getProjectGuidAsBytes();
        $record->is_active             = $model->isActive();
        $record->title                 = $model->title();
        $record->slug                  = $model->slug();
        $record->description           = $model->description();
        $record->run_before_every_item = $model->runBeforeEveryItem();
        $record->pipeline_items        = $items;

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
