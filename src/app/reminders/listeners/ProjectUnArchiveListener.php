<?php
declare(strict_types=1);

namespace src\app\reminders\listeners;

use corbomite\events\interfaces\EventInterface;
use src\app\reminders\interfaces\ReminderApiInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;

class ProjectUnArchiveListener implements EventListenerInterface
{
    private $reminderApi;

    public function __construct(ReminderApiInterface $reminderApi)
    {
        $this->reminderApi = $reminderApi;
    }

    public function call(EventInterface $event): void
    {
        /** @var ProjectBeforeUnArchiveEvent $event */

        $queryModel = $this->reminderApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        foreach ($this->reminderApi->fetchAll($queryModel) as $model) {
            if ($model->isActive()) {
                continue;
            }

            $model->isActive(true);
            $this->reminderApi->save($model);
        }
    }
}
