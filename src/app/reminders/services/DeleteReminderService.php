<?php
declare(strict_types=1);

namespace src\app\reminders\services;

use src\app\data\Reminder\Reminder;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Reminder\ReminderRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\reminders\events\ReminderAfterDeleteEvent;
use src\app\reminders\events\ReminderBeforeDeleteEvent;
use src\app\reminders\interfaces\ReminderModelInterface;

class DeleteReminderService
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
        $this->delete($model);
    }

    public function delete(ReminderModelInterface $model): void
    {
        $this->eventDispatcher->dispatch(new ReminderBeforeDeleteEvent($model));

        $this->ormFactory->makeOrm()->delete($this->fetchRecord($model));

        $this->eventDispatcher->dispatch(new ReminderAfterDeleteEvent($model));
    }

    private function fetchRecord(ReminderModelInterface $model): ReminderRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());
        return $this->buildQuery->build(Reminder::class, $params)->fetchRecord();
    }
}
