<?php

declare(strict_types=1);

namespace src\app\reminders\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use src\app\data\Reminder\Reminder;
use src\app\data\Reminder\ReminderRecord;
use src\app\reminders\events\ReminderAfterUnArchiveEvent;
use src\app\reminders\events\ReminderBeforeUnArchiveEvent;
use src\app\reminders\interfaces\ReminderModelInterface;

class UnArchiveReminderService
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
        $this->unArchive($model);
    }

    public function unArchive(ReminderModelInterface $model) : void
    {
        $this->eventDispatcher->dispatch(new ReminderBeforeUnArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new ReminderAfterUnArchiveEvent($model));
    }

    private function fetchRecord(ReminderModelInterface $model) : ReminderRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(Reminder::class, $params)->fetchRecord();
    }
}
