<?php
declare(strict_types=1);

namespace src\app\reminders\services;

use src\app\data\Reminder\Reminder;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Reminder\ReminderRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\reminders\interfaces\ReminderModelInterface;
use src\app\reminders\events\ReminderAfterUnArchiveEvent;
use src\app\reminders\events\ReminderBeforeUnArchiveEvent;

class UnArchiveReminderService
{
    private $buildQuery;
    private $ormFactory;
    private $eventDispatcher;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ReminderModelInterface $model): void
    {
        $this->unArchive($model);
    }

    public function unArchive(ReminderModelInterface $model): void
    {
        $this->eventDispatcher->dispatch(new ReminderBeforeUnArchiveEvent($model));

        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);

        $this->eventDispatcher->dispatch(new ReminderAfterUnArchiveEvent($model));
    }

    private function fetchRecord(ReminderModelInterface $model): ReminderRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(Reminder::class, $params)->fetchRecord();
    }
}
