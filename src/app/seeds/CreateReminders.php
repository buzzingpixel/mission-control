<?php
declare(strict_types=1);

use corbomite\di\Di;
use Phinx\Seed\AbstractSeed;
use src\app\projects\ProjectsApi;
use src\app\reminders\ReminderApi;

class CreateReminders extends AbstractSeed
{
    public function getDependencies()
    {
        return [
            'CreateProjects',
        ];
    }

    public function run()
    {
        $this->createReminder('Test Reminder 1', 'Test Message 1', '2019-03-01');
        $this->createReminder('Test Reminder 2', 'Test Message 2', '2019-03-02');
        $this->createReminder('Test Reminder 3', 'Test Message 3', '2019-03-03');
    }

    private function createReminder(string $title, string $msg, string $start)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $di = Di::diContainer();

        /** @noinspection PhpUnhandledExceptionInspection */
        $projectApi = $di->get(ProjectsApi::class);

        $projectQuery = $projectApi->makeQueryModel();

        $projectQuery->addWhere('title', 'Test Project 1');

        $project = $projectApi->fetchOne($projectQuery);

        /** @noinspection PhpUnhandledExceptionInspection */
        $reminderApi = $di->get(ReminderApi::class);

        $model = $reminderApi->createModel();

        $model->title($title);

        $model->message($msg);

        $model->startRemindingOn(DateTime::createFromFormat(
            'Y-m-d g:i:s a',
            $start . ' 12:00:00 am',
            new DateTimeZone(date_default_timezone_get())
        ));

        $model->projectGuid($project->guid());

        // Will throw an error if title already exists in database, which is
        // just what we want. We don't want to duplicate
        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $reminderApi->save($model);
        } catch (\Throwable $e) {
        }
    }
}
