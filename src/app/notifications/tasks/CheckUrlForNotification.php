<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use DateTime;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use Throwable;
use function getenv;
use function time;

class CheckUrlForNotification
{
    /** @var EmailApiInterface */
    private $emailApi;
    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;
    /** @var SendNotificationAdapterInterface[] */
    private $sendNotificationAdapters;

    /**
     * @param SendNotificationAdapterInterface[] $sendNotificationAdapters
     */
    public function __construct(
        EmailApiInterface $emailApi,
        MonitoredUrlsApiInterface $monitoredUrlsApi,
        array $sendNotificationAdapters = []
    ) {
        $this->emailApi                 = $emailApi;
        $this->monitoredUrlsApi         = $monitoredUrlsApi;
        $this->sendNotificationAdapters = $sendNotificationAdapters;
    }

    public function check(MonitoredUrlModelInterface $urlModel) : void
    {
        try {
            $this->innerCheck($urlModel);
        } catch (Throwable $e) {
            // if (getenv('DEV_MODE') === 'true') {
            //     /** @noinspection PhpUnhandledExceptionInspection */
            //     throw $e;
            // }

            $this->sendErrorEmail($e);
        }
    }

    private function sendErrorEmail(Throwable $e) : void
    {
        try {
            $emailModel = $this->emailApi->createEmailModel();
            $emailModel->toEmail(getenv('WEBMASTER_EMAIL_ADDRESS'));
            $emailModel->subject('An exception was thrown checking a URL for notification');
            $emailModel->messagePlainText(
                'While checking a URL for notification ' .
                "an exception was thrown.\n\n" .
                'File: ' . $e->getFile() . "\n" .
                'Line: ' . $e->getLine() . "\n" .
                'Message: ' . $e->getMessage()
            );

            $this->emailApi->sendEmail($emailModel);
        } catch (Throwable $e) {
        }
    }

    /**
     * @param mixed[] $context
     *
     * @throws Throwable
     */
    public function innerCheck(MonitoredUrlModelInterface $urlModel) : void
    {
        $incidentModel         = $this->getIncidentModel($urlModel->guid());
        $previousIncidentModel = $this->getIncidentModel($urlModel->guid(), true);

        if (! $incidentModel) {
            return;
        }

        // If the previous incident was pending and the current incident is up
        // We can stop here
        if ($previousIncidentModel &&
            (
                $previousIncidentModel->eventType() === 'pending' &&
                $incidentModel->eventType() === 'up'
            )
        ) {
            return;
        }

        // If the event is pending, we won't want to send a notification about that
        if ($incidentModel->eventType() === 'pending') {
            return;
        }

        // If the event is up and we've already notified about it, we're done here
        if ($incidentModel->lastNotificationAt() && $incidentModel->eventType() === 'up') {
            return;
        }

        $timestamp = 0;

        // If there's already been a down notification, we'll wait one hour
        if ($incidentModel->lastNotificationAt()) {
            $oneHourInSeconds = 3600;
            $timestamp        = $incidentModel->lastNotificationAt()->getTimestamp() + $oneHourInSeconds;
        }

        if (time() < $timestamp) {
            return;
        }

        $subject = '';

        if ($incidentModel->lastNotificationAt()) {
            $subject = 'Continuing Down Time: ';
        }

        $subject .= $urlModel->title() . ' (' . $urlModel->url() . ') is ';
        $subject .= $incidentModel->eventType();

        $message  = 'URL Title: ' . $urlModel->title() . "\n";
        $message .= 'URL: ' . $urlModel->url() . "\n";
        $message .= 'Status Code: ' . $incidentModel->statusCode() . "\n";
        $message .= 'Message: ' . $incidentModel->message();

        foreach ($this->sendNotificationAdapters as $adapter) {
            $this->sendNotificationWithAdapter(
                $adapter,
                $subject,
                $message,
                $incidentModel->eventType(),
                $urlModel->slug(),
                $urlModel->url()
            );
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $incidentModel->lastNotificationAt(new DateTime());

        $this->monitoredUrlsApi->saveIncident($incidentModel);
    }

    private function sendNotificationWithAdapter(
        SendNotificationAdapterInterface $adapter,
        string $subject,
        string $message,
        string $eventType,
        string $urlSlug,
        string $url
    ) : void {
        $status = '';

        if ($eventType === 'down') {
            $status = 'bad';
        } elseif ($eventType === 'up') {
            $status = 'good';
        }

        $adapter->send(
            $subject,
            $message,
            [
                'status' => $status,
                'urls' => [
                    [
                        'content' => 'View Incidents',
                        'href' => getenv('SITE_URL') . '/monitored-urls/view/' . $urlSlug,
                    ],
                    [
                        'content' => 'Go To URL',
                        'href' => $url,
                    ],
                ],
            ]
        );
    }

    private function getIncidentModel(string $urlGuid, bool $previous = false) : ?MonitoredUrlIncidentModelInterface
    {
        $queryModel = $this->monitoredUrlsApi->makeQueryModel();

        $queryModel->addWhere('monitored_url_guid', $this->monitoredUrlsApi->uuidToBytes($urlGuid));

        if ($previous) {
            $queryModel->offset(1);
        }

        $queryModel->addOrder('event_at');

        return $this->monitoredUrlsApi->fetchOneIncident($queryModel);
    }
}
