<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;

class CheckUrlsForNotificationsTask
{
    public const BATCH_NAME  = 'checkUrlsForNotifications';
    public const BATCH_TITLE = 'Check Urls For Notifications';

    /** @var MonitoredUrlsApiInterface */
    private $monitoredUrlsApi;
    /** @var CheckUrlForNotification */
    private $checkUrlForNotification;

    public function __construct(
        MonitoredUrlsApiInterface $monitoredUrlsApi,
        CheckUrlForNotification $checkUrlForNotification
    ) {
        $this->monitoredUrlsApi        = $monitoredUrlsApi;
        $this->checkUrlForNotification = $checkUrlForNotification;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke() : void
    {
        $queryModel = $this->monitoredUrlsApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->monitoredUrlsApi->fetchAll($queryModel) as $model) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->checkUrlForNotification->check($model);
        }
    }
}
