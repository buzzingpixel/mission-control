<?php

declare(strict_types=1);

namespace src\app\tickets\services;

use Atlas\Table\Exception as AtlasTableException;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\interfaces\EventDispatcherInterface;
use src\app\data\Ticket\Ticket;
use src\app\data\Ticket\TicketRecord;
use src\app\tickets\events\TicketAfterSaveEvent;
use src\app\tickets\events\TicketBeforeSaveEvent;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketModelContract;

class SaveTicketService
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
    public function save(TicketModelContract $model) : void
    {
        $this->validate($model);

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());

        /** @noinspection PhpUnhandledExceptionInspection */
        $existingRecord = $this->buildQuery->build(Ticket::class, $fetchModel)
            ->fetchRecord();

        if (! $existingRecord) {
            $this->eventDispatcher->dispatch(new TicketBeforeSaveEvent($model, true));

            $this->saveNew($model);

            $this->eventDispatcher->dispatch(new TicketAfterSaveEvent($model, true));

            return;
        }

        $this->eventDispatcher->dispatch(new TicketBeforeSaveEvent($model));

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpParamsInspection */
        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new TicketAfterSaveEvent($model));
    }

    /**
     * @throws InvalidModel
     */
    private function validate(TicketModelContract $model) : void
    {
        if (! $model->createdByUser() ||
            ! $model->title()
        ) {
            throw new InvalidModel();
        }
    }

    private function saveNew(TicketModelContract $model) : void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Ticket::class);

        $record->guid = $model->getGuidAsBytes();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->finalSave($model, $record);
    }

    /**
     * @throws AtlasTableException
     */
    private function finalSave(TicketModelContract $model, TicketRecord $record) : void
    {
        $record->created_by_user_guid = $model->createdByUser()->getGuidAsBytes();

        if ($model->assignedToUser()) {
            $record->assigned_to_user_guid = $model->assignedToUser()->getGuidAsBytes();
        }

        $record->title = $model->title();

        $record->content = $model->content();

        $record->status = $model->status();

        $record->added_at_utc = $model->addedAt()->format('Y-m-d H:i:s');

        $record->resolved_at_utc = null;

        if ($model->resolvedAt()) {
            $record->resolved_at_utc = $model->resolvedAt()->format('Y-m-d H:i:s');
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
