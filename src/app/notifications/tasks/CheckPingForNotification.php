<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use DateTime;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use src\app\pings\interfaces\PingApiInterface;
use src\app\pings\interfaces\PingModelInterface;
use Throwable;
use function getenv;
use function time;

class CheckPingForNotification
{
    /** @var PingApiInterface */
    private $pingApi;
    /** @var SendNotificationAdapterInterface[] */
    private $sendNotificationAdapters;

    /**
     * @param SendNotificationAdapterInterface[] $sendNotificationAdapters
     */
    public function __construct(
        PingApiInterface $pingApi,
        array $sendNotificationAdapters = []
    ) {
        $this->pingApi                  = $pingApi;
        $this->sendNotificationAdapters = $sendNotificationAdapters;
    }

    /**
     * @throws Throwable
     */
    public function check(PingModelInterface $pingModel) : void
    {
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
            $timestamp        = $pingModel->lastNotificationAt()->getTimestamp() + $oneHourInSeconds;
        }

        // It's less than an hour we can stop
        if (time() < $timestamp) {
            return;
        }

        $this->sendMissingReminder($pingModel);
    }

    /**
     * @throws Throwable
     */
    private function sendUpNotification(PingModelInterface $pingModel) : void
    {
        $message = 'The Ping ' . $pingModel->title() . ' is now healthy';

        foreach ($this->sendNotificationAdapters as $adapter) {
            $adapter->send($message, $message, [
                'status' => 'good',
                'urls' => [
                    [
                        'content' => 'View Ping',
                        'href' => getenv('SITE_URL') . '/pings/view/' . $pingModel->slug(),
                    ],
                ],
            ]);
        }

        $pingModel->clearLastNotificationAt();

        $this->pingApi->save($pingModel);
    }

    /**
     * @throws Throwable
     */
    private function sendMissingNotification(PingModelInterface $pingModel) : void
    {
        $message = 'The Ping ' . $pingModel->title() . ' is missing';

        foreach ($this->sendNotificationAdapters as $adapter) {
            $adapter->send($message, $message, [
                'status' => 'bad',
                'urls' => [[
                    'content' => 'View Ping',
                    'href' => getenv('SITE_URL') . '/pings/view/' . $pingModel->slug(),
                ],
                ],
            ]);
        }

        $this->saveLastNotificationAt($pingModel);
    }

    /**
     * @throws Throwable
     */
    private function sendMissingReminder(PingModelInterface $pingModel) : void
    {
        $message = 'REMINDER: The Ping ' . $pingModel->title() . ' is missing';

        foreach ($this->sendNotificationAdapters as $adapter) {
            $adapter->send($message, $message, [
                'status' => 'bad',
                'urls' => [[
                    'content' => 'View Ping',
                    'href' => getenv('SITE_URL') . '/pings/view/' . $pingModel->slug(),
                ],
                ],
            ]);
        }

        $this->saveLastNotificationAt($pingModel);
    }

    /**
     * @throws Throwable
     */
    private function saveLastNotificationAt(PingModelInterface $pingModel) : void
    {
        $pingModel->lastNotificationAt(new DateTime());
        $this->pingApi->save($pingModel);
    }
}
