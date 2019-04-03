<?php

declare(strict_types=1);

namespace src\app\reminders\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\Reminder\Reminder;
use src\app\data\Reminder\ReminderRecord;
use src\app\reminders\events\ReminderAfterArchiveEvent;
use src\app\reminders\events\ReminderBeforeArchiveEvent;
use src\app\reminders\interfaces\ReminderModelInterface;

class ArchiveReminderService
{
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ReminderModelInterface $model) : void
    {
        $this->archive($model);
    }

    public function archive(ReminderModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new ReminderBeforeArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 0;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new ReminderAfterArchiveEvent($model));
    }

    private function fetchRecord(ReminderModelInterface $model) : ReminderRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(Reminder::class, $params)->fetchRecord();
    }
}
