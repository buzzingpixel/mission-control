<?php
declare(strict_types=1);

namespace src\app\reminders\listeners;

use corbomite\events\interfaces\EventInterface;
use src\app\reminders\interfaces\ReminderApiInterface;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use corbomite\events\interfaces\EventListenerInterface;

class ProjectArchiveListener implements EventListenerInterface
{
    private $reminderApi;

    public function __construct(ReminderApiInterface $reminderApi)
    {
        $this->reminderApi = $reminderApi;
    }

    public function call(EventInterface $event): void
    {
        /** @var ProjectBeforeArchiveEvent $event */

        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->reminderApi->fetchAll($queryModel) as $model) {
            if (! $model->isActive()) {
                continue;
            }

            $model->isActive(false);
            $this->reminderApi->save($model);
        }
    }
}
