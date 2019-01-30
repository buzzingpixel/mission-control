<?php
declare(strict_types=1);

namespace src\app\notifications\tasks;

use DateTime;
use src\app\pings\interfaces\PingApiInterface;
use src\app\pings\interfaces\PingModelInterface;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;

class CheckPingForNotificationTask
{
    public const BATCH_NAME = 'checkPingsForNotifications';
    public const BATCH_TITLE = 'Check Pings for Notifications';

    private $pingApi;

    /** @var SendNotificationAdapterInterface[] */
    private $sendNotificationAdapters;

    public function __construct(
        PingApiInterface $pingApi,
        array $sendNotificationAdapters = []
    ) {
        $this->pingApi = $pingApi;
        $this->sendNotificationAdapters = $sendNotificationAdapters;
    }

    public function __invoke(array $context): void
    {
        $pingModel = $this->getModel($context['guid']);

        // If there's no error and no last notification, we can stop here
        if (! $pingModel->hasError() && ! $pingModel->lastNotificationAt()) {
            return;
        }

        // If no error, we want to notify that everything is good now
        if (! $pingModel->hasError()) {
            $this->sendUpNotification($pingModel);
            return;
        }

        // We know now that we have an error

        // If there's no last notification we can send the notification
        if (! $pingModel->lastNotificationAt()) {
            $this->sendMissingNotification($pingModel);
            return;
        }

        // Now we need to determine if it's been more than an hour since last

        $timestamp = 0;

        // If there's already been a down notification, we'll wait one hour
        if ($pingModel->lastNotificationAt()) {
            $oneHourInSeconds = 3600;
            $timestamp = $pingModel->lastNotificationAt()->getTimestamp() + $oneHourInSeconds;
        }

        // It's less than an hour we can stop
        if (time() < $timestamp) {
            return;
        }

        $this->sendMissingReminder($pingModel);
    }

    private function sendUpNotification(PingModelInterface $pingModel)
    {
        $message = 'The Ping ' . $pingModel->title() . ' is now healthy';

        foreach ($this->sendNotificationAdapters as $adapter) {
            $adapter->send($message, $message, [
                'status' => 'good',
                'urls' => [[
                    'content' => 'View Ping',
                    'href' => getenv('SITE_URL') . '/pings/view/' . $pingModel->slug(),
                ]]
            ]);
        }

        $pingModel->clearLastNotificationAt();

        $this->pingApi->save($pingModel);
    }

    private function sendMissingNotification(PingModelInterface $pingModel)
    {
        $message = 'The Ping ' . $pingModel->title() . ' is missing';

        foreach ($this->sendNotificationAdapters as $adapter) {
            $adapter->send($message, $message, [
                'status' => 'bad',
                'urls' => [[
                    'content' => 'View Ping',
                    'href' => getenv('SITE_URL') . '/pings/view/' . $pingModel->slug(),
                ]]
            ]);
        }

        $this->saveLastNotificationAt($pingModel);
    }

    private function sendMissingReminder(PingModelInterface $pingModel)
    {
        $message = 'REMINDER: The Ping ' . $pingModel->title() . ' is missing';

        foreach ($this->sendNotificationAdapters as $adapter) {
            $adapter->send($message, $message, [
                'status' => 'bad',
                'urls' => [[
                    'content' => 'View Ping',
                    'href' => getenv('SITE_URL') . '/pings/view/' . $pingModel->slug(),
                ]]
            ]);
        }

        $this->saveLastNotificationAt($pingModel);
    }

    private function saveLastNotificationAt(PingModelInterface $pingModel)
    {
        $pingModel->lastNotificationAt(new DateTime());
        $this->pingApi->save($pingModel);
    }

    private function getModel(string $guid): PingModelInterface
    {
        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addWhere('guid', $this->pingApi->uuidToBytes($guid));
        return $this->pingApi->fetchOne($queryModel);
    }
}
