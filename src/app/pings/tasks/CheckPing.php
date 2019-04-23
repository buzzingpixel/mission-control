<?php

declare(strict_types=1);

namespace src\app\pings\tasks;

use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use src\app\pings\interfaces\PingApiInterface;
use src\app\pings\interfaces\PingModelInterface;
use Throwable;
use function getenv;
use function time;

class CheckPing
{
    /** @var PingApiInterface */
    private $pingApi;
    /** @var EmailApiInterface */
    private $emailApi;

    public function __construct(
        PingApiInterface $pingApi,
        EmailApiInterface $emailApi
    ) {
        $this->pingApi  = $pingApi;
        $this->emailApi = $emailApi;
    }

    public function checkPing(PingModelInterface $model) : void
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
            $emailModel->subject('An exception was thrown checking a Ping');
            $emailModel->messagePlainText(
                'While checking a Ping ' .
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
    private function innerRun(PingModelInterface $model) : void
    {
        $time               = time();
        $expectEverySeconds = $model->expectEvery() * 60;
        $warnAfterSeconds   = $model->warnAfter() * 60;
        $expectTime         = $model->lastPingAt()->getTimestamp() + $expectEverySeconds;
        $warnTime           = $expectTime + $warnAfterSeconds;

        $pingPastWarning = $time > $warnTime;
        $pingPastExpect  = $time > $expectTime;

        if ($pingPastWarning) {
            $model->pendingError(true);
            $model->hasError(true);
            $this->pingApi->save($model);

            return;
        }

        if ($pingPastExpect) {
            $model->pendingError(true);
            $model->hasError(false);
            $this->pingApi->save($model);

            return;
        }

        $model->pendingError(false);
        $model->hasError(false);
        $this->pingApi->save($model);
    }
}
