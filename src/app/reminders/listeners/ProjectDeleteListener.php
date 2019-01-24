<?php
declare(strict_types=1);

namespace src\app\reminders\listeners;

use corbomite\events\interfaces\EventInterface;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use src\app\reminders\interfaces\ReminderApiInterface;
use corbomite\events\interfaces\EventListenerInterface;

class ProjectDeleteListener implements EventListenerInterface
{
    private $reminderApi;

    public function __construct(ReminderApiInterface $reminderApi)
    {
        $this->reminderApi = $reminderApi;
    }

    public function call(EventInterface $event): void
    {
        /** @var ProjectBeforeDeleteEvent $event */

        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->reminderApi->fetchAll($queryModel) as $model) {
            $this->reminderApi->delete($model);
        }
    }
}
