<?php

declare(strict_types=1);

namespace src\app\reminders\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\reminders\interfaces\ReminderApiInterface;

class ProjectDeleteListener implements EventListenerInterface
{
    /** @var ReminderApiInterface */
    private $reminderApi;

    public function __construct(ReminderApiInterface $reminderApi)
    {
        $this->reminderApi = $reminderApi;
    }

    public function call(EventInterface $event) : void
    {
        /** @var ProjectBeforeDeleteEvent $event */

        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->reminderApi->fetchAll($queryModel) as $model) {
            $this->reminderApi->delete($model);
        }
    }
}
