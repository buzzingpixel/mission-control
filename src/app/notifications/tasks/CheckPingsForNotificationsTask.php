<?php

declare(strict_types=1);

namespace src\app\notifications\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use src\app\pings\interfaces\PingApiInterface;

class CheckPingsForNotificationsTask
{
    public const BATCH_NAME  = 'checkPingsForNotifications';
    public const BATCH_TITLE = 'Check Pings For Notifications';

    /** @var PingApiInterface */
    private $pingApi;
    /** @var CheckPingForNotification */
    private $checkPingForNotification;

    public function __construct(
        PingApiInterface $pingApi,
        CheckPingForNotification $checkPingForNotification
    ) {
        $this->pingApi                  = $pingApi;
        $this->checkPingForNotification = $checkPingForNotification;
    }

    /**
     * @throws InvalidActionQueueBatchModel
     */
    public function __invoke() : void
    {
        $queryModel = $this->pingApi->makeQueryModel();
        $queryModel->addOrder('title', 'asc');
        $queryModel->addWhere('is_active', '1');

        foreach ($this->pingApi->fetchAll($queryModel) as $model) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->checkPingForNotification->check($model);
        }
    }
}
