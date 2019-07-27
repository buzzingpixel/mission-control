<?php

declare(strict_types=1);

namespace src\app\tickets\services;

use Atlas\Table\Exception as AtlasTableException;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\interfaces\EventDispatcherInterface;
use DateTimeImmutable;
use DateTimeZone;
use src\app\data\TicketThreadItem\TicketThreadItem;
use src\app\data\TicketThreadItem\TicketThreadItemRecord;
use src\app\tickets\events\TicketThreadItemAfterSaveEvent;
use src\app\tickets\events\TicketThreadItemBeforeSaveEvent;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use function dd;

class SaveTicketThreadItemService
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
     * @throws InvalidModel
     */
    public function save(TicketThreadItemModelContract $model) : void
    {
        $this->validate($model);

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());

        /** @noinspection PhpUnhandledExceptionInspection */
        $existingRecord = $this->buildQuery->build(TicketThreadItem::class, $fetchModel)
            ->fetchRecord();

        if (! $existingRecord) {
            $model->hasBeenModified(false);

            $model->clearModifiedAt();

            $this->eventDispatcher->dispatch(new TicketThreadItemBeforeSaveEvent($model, true));

            $this->saveNew($model);

            $this->eventDispatcher->dispatch(new TicketThreadItemAfterSaveEvent($model, true));

            return;
        }

        $model->hasBeenModified(true);

        /** @noinspection PhpUnhandledExceptionInspection */
        $model->modifiedAt(new DateTimeImmutable('now', new DateTimeZone('UTC')));

        $this->eventDispatcher->dispatch(new TicketThreadItemBeforeSaveEvent($model));

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpParamsInspection */
        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new TicketThreadItemAfterSaveEvent($model));

        dd('save existing record');
    }

    /**
     * @throws InvalidModel
     */
    private function validate(TicketThreadItemModelContract $model) : void
    {
        if (! $model->ticket() ||
            ! $model->user() ||
            ! $model->content()
        ) {
            throw new InvalidModel();
        }
    }

    private function saveNew(TicketThreadItemModelContract $model) : void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(TicketThreadItem::class);

        $record->guid = $model->getGuidAsBytes();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->finalSave($model, $record);
    }

    /**
     * @throws AtlasTableException
     */
    private function finalSave(TicketThreadItemModelContract $model, TicketThreadItemRecord $record) : void
    {
        $record->ticket_guid = $model->ticket()->getGuidAsBytes();

        $record->user_guid = $model->user()->getGuidAsBytes();

        $record->content = $model->content();

        $record->added_at_utc = $model->addedAt()->format('Y-m-d H:i:s');

        $record->has_been_modified = $model->hasBeenModified();

        $record->modified_at_utc = null;

        if ($model->modifiedAt()) {
            $record->modified_at_utc = $model->modifiedAt()->format('Y-m-d H:i:s');
        }

        try {
            $this->ormFactory->makeOrm()->persist($record);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() === 'Expected 1 row affected, actual 0.') {
                return;
            }

            throw $e;
        }
    }
}
