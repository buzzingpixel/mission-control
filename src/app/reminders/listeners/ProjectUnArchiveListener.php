<?php

declare(strict_types=1);

namespace src\app\reminders\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;
use src\app\reminders\interfaces\ReminderApiInterface;

class ProjectUnArchiveListener implements EventListenerInterface
{
    /** @var ReminderApiInterface */
    private $reminderApi;

    public function __construct(ReminderApiInterface $reminderApi)
    {
        $this->reminderApi = $reminderApi;
    }

    public function call(EventInterface $event) : void
    {
        /** @var ProjectBeforeUnArchiveEvent $event */

        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->reminderApi->fetchAll($queryModel) as $model) {
            if ($model->isActive()) {
                continue;
            }

            $model->isActive(true);

            /** @noinspection PhpUnhandledExceptionInspection */
            $this->reminderApi->save($model);
        }
    }
}
