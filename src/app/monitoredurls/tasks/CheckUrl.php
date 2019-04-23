<?php

declare(strict_types=1);

namespace src\app\monitoredurls\tasks;

use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use DateTime;
use DateTimeZone;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use Throwable;
use function getenv;

class CheckUrl
{
    /** @var EmailApiInterface */
    private $emailApi;
    /** @var GuzzleClientNoHttpErrors */
    private $guzzleClient;
    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;

    public function __construct(
        EmailApiInterface $emailApi,
        GuzzleClientNoHttpErrors $guzzleClient,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->emailApi         = $emailApi;
        $this->guzzleClient     = $guzzleClient;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    /** @var ?string */
    private $url;

    public function checkUrl(MonitoredUrlModelInterface $model) : void
    {
        try {
            $this->innerRun($model);
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
            $emailModel->subject('An exception was thrown checking a URL');
            $emailModel->messagePlainText(
                'While checking a URL ' .
                ($this->url ? '(' . $this->url . ') ' : '') .
                "an exception was thrown.\n\n" .
                'File: ' . $e->getFile() . "\n" .
                'Line: ' . $e->getLine() . "\n" .
                'Message: ' . $e->getMessage()
            );

            $this->emailApi->sendEmail($emailModel);
        } catch (Throwable $e) {
        }
    }

    private function innerRun(MonitoredUrlModelInterface $model) : void
    {
        $this->url = $model->url();

        try {
            $response = $this->guzzleClient->get($model->url(), [
                'timeout' => (float) (getenv('GUZZLE_TIMEOUT') ?: 8),
            ]);

            $statusCode = $response->getStatusCode();

            $hasError = $statusCode !== 200;

            $message = 'The URL ' . $model->url() . ' returned a status code of ' . $statusCode;
        } catch (Throwable $e) {
            $statusCode = '';
            $hasError   = true;
            $message    = 'A Guzzle Exception Occurred: ' . $e->getMessage();
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $model->checkedAt(new DateTime('now', new DateTimeZone('UTC')));

        /**
         * If the site has returned an error, and the model already has error
         * no further action needs to be taken. We'll just save the model to
         * update the time last checked
         */
        if ($hasError && $model->hasError()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->monitoredUrlsApi->save($model);

            return;
        }

        /**
         * We'll go ahead and create our incident model
         */
        $incident = $this->monitoredUrlsApi->createIncidentModel();
        $incident->monitoredUrlGuid($model->guid());
        $incident->statusCode((string) $statusCode);
        $incident->message($message);
        /** @noinspection PhpUnhandledExceptionInspection */
        $incident->eventAt(new DateTime('now', new DateTimeZone('UTC')));

        /**
         * If the site has an error and the model does not have an error pending
         * we'll set it to pending and add a pending incident
         */
        if ($hasError && ! $model->pendingError()) {
            $incident->eventType('pending');
            $model->pendingError(true);

            /** @noinspection PhpUnhandledExceptionInspection */
            $this->monitoredUrlsApi->saveIncident($incident);
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->monitoredUrlsApi->save($model);

            return;
        }

        /**
         * If the site has an error, we already know the model has a pending
         * error, so this isn't "fake news". The site is down and we'll proceed
         * with that
         */
        if ($hasError) {
            $incident->eventType('down');
            $model->hasError(true);

            /** @noinspection PhpUnhandledExceptionInspection */
            $this->monitoredUrlsApi->saveIncident($incident);
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->monitoredUrlsApi->save($model);

            return;
        }

        /**
         * We know now the site has no error. We'll get the most recent incident
         * and if it's not an up incident, we need to add an up incident. Then
         * we'll save the model to update the checked time, and potentially
         * whether it has errors
         */

        $lastIncident = $this->getLastIncident($model->guid());

        if ($lastIncident && $lastIncident->eventType() !== 'up') {
            $incident->eventType('up');

            /** @noinspection PhpUnhandledExceptionInspection */
            $this->monitoredUrlsApi->saveIncident($incident);
        }

        $model->hasError(false);
        $model->pendingError(false);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->monitoredUrlsApi->save($model);
    }

    private function getLastIncident(string $urlGuid) : ?MonitoredUrlIncidentModelInterface
    {
        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addWhere('monitored_url_guid', $this->monitoredUrlsApi->uuidToBytes($urlGuid));
        $queryModel->addOrder('event_at');
        $queryModel->limit(1);

        return $this->monitoredUrlsApi->fetchIncidents($queryModel)[0] ?? null;
    }
}
