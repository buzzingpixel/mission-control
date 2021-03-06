<?php

declare(strict_types=1);

namespace src\app\notifications\notificationadapters;

use buzzingpixel\corbomitemailer\exceptions\InvalidEmailModelException;
use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;

class SendEmailNotificationAdapter implements SendNotificationAdapterInterface
{
    /** @var EmailApiInterface */
    private $emailApi;
    /** @var NotificationEmailsApiInterface */
    private $notificationEmailsApi;

    public function __construct(
        EmailApiInterface $emailApi,
        NotificationEmailsApiInterface $notificationEmailsApi
    ) {
        $this->emailApi              = $emailApi;
        $this->notificationEmailsApi = $notificationEmailsApi;
    }

    /**
     * @param mixed[] $context
     *
     * @throws InvalidEmailModelException
     */
    public function send(string $subject, string $message, array $context = []) : void
    {
        $queryModel = $this->notificationEmailsApi->makeQueryModel();
        $queryModel->addWhere('is_active', '1');
        $notificationEmailModels = $this->notificationEmailsApi->fetchAll($queryModel);

        foreach ($notificationEmailModels as $model) {
            $this->sendToEmailAddress($model->emailAddress(), $subject, $message);
        }
    }

    /**
     * @throws InvalidEmailModelException
     */
    private function sendToEmailAddress(
        string $emailAddress,
        string $subject,
        string $message
    ) : void {
        $emailModel = $this->emailApi->createEmailModel();

        $emailModel->toEmail($emailAddress);

        $emailModel->subject($subject);

        $emailModel->messagePlainText($message);

        $this->emailApi->addEmailToQueue($emailModel);
    }
}
