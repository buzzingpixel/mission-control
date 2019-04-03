<?php

declare(strict_types=1);

namespace src\app\reminders\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use DateTime;
use DateTimeZone;
use src\app\data\Reminder\Reminder;
use src\app\data\Reminder\ReminderRecord;
use src\app\reminders\interfaces\ReminderModelInterface;
use src\app\reminders\models\ReminderModel;

class FetchReminderService
{
    /** @var BuildQueryInterface */
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return ReminderModelInterface[]
     */
    public function __invoke(QueryModelInterface $params) : array
    {
        return $this->fetch($params);
    }

    /**
     * @return ReminderModelInterface[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new ReminderModel();

            $model->setGuidAsBytes($record->guid);

            if ($record->project_guid) {
                $model->setProjectGuidAsBytes($record->project_guid);
            }

            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->title($record->title);
            $model->slug($record->slug);
            $model->message($record->message);

            /** @noinspection PhpUnhandledExceptionInspection */
            $model->startRemindingOn(new DateTime(
                $record->start_reminding_on,
                new DateTimeZone($record->start_reminding_on_time_zone)
            ));

            $lastReminderSent = $record->last_reminder_sent;

            if ($lastReminderSent) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->lastReminderSent(new DateTime(
                    $lastReminderSent,
                    new DateTimeZone($record->last_reminder_sent_time_zone)
                ));
            }

            /** @noinspection PhpUnhandledExceptionInspection */
            $model->addedAt(new DateTime(
                $record->added_at,
                new DateTimeZone($record->added_at_time_zone)
            ));

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return ReminderRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(Reminder::class, $params)->fetchRecords();
    }
}
