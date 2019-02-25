<?php
declare(strict_types=1);

namespace src\app\reminders\services;

use DateTimeZone;
use Cocur\Slugify\Slugify;
use src\app\data\Reminder\Reminder;
use corbomite\db\Factory as OrmFactory;
use src\app\data\Reminder\ReminderRecord;
use corbomite\db\interfaces\BuildQueryInterface;
use Atlas\Table\Exception as AtlasTableException;
use src\app\reminders\events\ReminderAfterSaveEvent;
use src\app\reminders\events\ReminderBeforeSaveEvent;
use src\app\reminders\interfaces\ReminderModelInterface;
use corbomite\events\interfaces\EventDispatcherInterface;
use src\app\reminders\exceptions\InvalidReminderModelException;
use src\app\reminders\exceptions\ReminderNameNotUniqueException;

class SaveReminderService
{
    private $slugify;
    private $ormFactory;
    private $buildQuery;
    private $eventDispatcher;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidReminderModelException
     * @throws ReminderNameNotUniqueException
     */
    public function __invoke(ReminderModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidReminderModelException
     * @throws ReminderNameNotUniqueException
     */
    public function save(ReminderModelInterface $model): void
    {
        if (! $model->title() || ! $model->startRemindingOn()) {
            throw new InvalidReminderModelException();
        }

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(Reminder::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new ReminderNameNotUniqueException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(Reminder::class, $fetchModel)->fetchRecord();

        if (! $existingRecord) {
            $this->eventDispatcher->dispatch(new ReminderBeforeSaveEvent($model, true));

            $this->saveNew($model);

            $this->eventDispatcher->dispatch(new ReminderAfterSaveEvent($model, true));

            return;
        }

        $this->eventDispatcher->dispatch(new ReminderBeforeSaveEvent($model));

        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new ReminderAfterSaveEvent($model));
    }

    private function saveNew(ReminderModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Reminder::class);

        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        ReminderModelInterface $model,
        ReminderRecord $record
    ): void {
        $startRemindingOn = $model->startRemindingOn();
        $lastReminderSent = $model->lastReminderSent();
        $addedAt = $model->addedAt();

        $startRemindingOn->setTimezone(new DateTimeZone('UTC'));
        $addedAt->setTimezone(new DateTimeZone('UTC'));

        $record->project_guid = $model->getProjectGuidAsBytes();
        $record->is_active = $model->isActive();
        $record->title = $model->title();
        $record->slug = $model->slug();
        $record->message = $model->message();
        $record->start_reminding_on = $startRemindingOn->format('Y-m-d H:i:s');
        $record->start_reminding_on_time_zone = $startRemindingOn->getTimezone()->getName();

        if ($lastReminderSent) {
            $lastReminderSent->setTimezone(new DateTimeZone('UTC'));
            $record->last_reminder_sent = $lastReminderSent->format('Y-m-d H:i:s');
            $record->last_reminder_sent_time_zone = $lastReminderSent->getTimezone()->getName();
        }

        if (! $lastReminderSent) {
            $record->last_reminder_sent = null;
            $record->last_reminder_sent_time_zone = null;
        }

        $record->added_at = $addedAt->format('Y-m-d H:i:s');
        $record->added_at_time_zone = $addedAt->getTimezone()->getName();

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
