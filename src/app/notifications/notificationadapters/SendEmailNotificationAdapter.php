<?php
declare(strict_types=1);

namespace src\app\notifications\notificationadapters;

use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use buzzingpixel\corbomitemailer\exceptions\InvalidEmailModelException;
use src\app\notificationemails\interfaces\NotificationEmailsApiInterface;

class SendEmailNotificationAdapter implements SendNotificationAdapterInterface
{
    private $emailApi;
    private $notificationEmailsApi;

    public function __construct(
        EmailApiInterface $emailApi,
        NotificationEmailsApiInterface $notificationEmailsApi
    ) {
        $this->emailApi = $emailApi;
        $this->notificationEmailsApi = $notificationEmailsApi;
    }

    /**
     * @throws InvalidEmailModelException
     */
    public function send(string $subject, string $message)
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
    ) {
        $emailModel = $this->emailApi->createEmailModel();

        $emailModel->toEmail($emailAddress);

        $emailModel->subject($subject);

        $emailModel->messagePlainText($message);

        $this->emailApi->addEmailToQueue($emailModel);
    }
}
