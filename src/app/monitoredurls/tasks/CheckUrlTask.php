<?php
declare(strict_types=1);

namespace src\app\monitoredurls\tasks;

use DateTime;
use Throwable;
use DateTimeZone;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use \buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;

class CheckUrlTask
{
    public const BATCH_NAME = 'checkUrls';
    public const BATCH_TITLE = 'Check URLs';

    private $emailApi;
    private $guzzleClient;
    private $monitoredUrlsApi;

    public function __construct(
        EmailApiInterface $emailApi,
        GuzzleClientNoHttpErrors $guzzleClient,
        MonitoredUrlsApiInterface $monitoredUrlsApi
    ) {
        $this->emailApi = $emailApi;
        $this->guzzleClient = $guzzleClient;
        $this->monitoredUrlsApi = $monitoredUrlsApi;
    }

    private $url;

    public function __invoke(array $context): void
    {
        try {
            $this->innerRun($context);
        } catch (Throwable $e) {
            if (getenv('DEV_MODE') === 'true') {
                throw $e;
            }

            $this->sendErrorEmail($e);
        }
    }

    private function sendErrorEmail(Throwable $e): void
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

    /**
     * @throws Throwable
     */
    public function innerRun(array $context): void
    {
        $model = $this->getModel($context['guid']);

        /**
         * In case an unknown error is thrown, when the email is sent out we
         * can know what URL we were checking
         */
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
            $hasError = true;
            $message = 'A Guzzle Exception Occurred: ' . $e->getMessage();
        }

        $model->checkedAt(new DateTime('now', new DateTimeZone('UTC')));

        /**
         * If the site has returned an error, and the model already has error
         * no further action needs to be taken. We'll just save the model to
         * update the time last checked
         */
        if ($hasError && $model->hasError()) {
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
        $incident->eventAt(new DateTime('now', new DateTimeZone('UTC')));

        /**
         * If the site has an error and the model does not have an error pending
         * we'll set it to pending and add a pending incident
         */
        if ($hasError && ! $model->pendingError()) {
            $incident->eventType('pending');
            $model->pendingError(true);

            $this->monitoredUrlsApi->saveIncident($incident);
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

            $this->monitoredUrlsApi->saveIncident($incident);
            $this->monitoredUrlsApi->save($model);

            return;
        }

        /**
         * We know now the site has no error. We'll get the most recent incident
         * and if it's not an up incident, we need to add an up incident. Then
         * we'll save the model to update the checked time, and potentially
         * whether it has errors
         */

        $lastIncident = $this->getLastIncident($context['guid']);

        if ($lastIncident && $lastIncident->eventType() !== 'up') {
            $incident->eventType('up');

            $this->monitoredUrlsApi->saveIncident($incident);
        }

        $model->hasError(false);
        $model->pendingError(false);

        $this->monitoredUrlsApi->save($model);
    }

    private function getModel(string $guid): MonitoredUrlModelInterface
    {
        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addWhere('guid', $this->monitoredUrlsApi->uuidToBytes($guid));
        return $this->monitoredUrlsApi->fetchOne($queryModel);
    }

    private function getLastIncident(string $urlGuid): ?MonitoredUrlIncidentModelInterface
    {
        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addWhere('monitored_url_guid', $this->monitoredUrlsApi->uuidToBytes($urlGuid));
        $queryModel->addOrder('event_at');
        $queryModel->limit(1);
        return $this->monitoredUrlsApi->fetchIncidents($queryModel)[0] ?? null;
    }
}
