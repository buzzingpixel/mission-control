<?php

declare(strict_types=1);

namespace src\app\pings\tasks;

use corbomite\queue\exceptions\InvalidActionQueueBatchModel;
use src\app\pings\interfaces\PingApiInterface;

class CheckPingsTask
{
    public const BATCH_NAME  = 'checkPings';
    public const BATCH_TITLE = 'Check Pings';

    /** @var PingApiInterface */
    private $pingApi;
    /** @var CheckPing */
    private $checkPing;

    public function __construct(
        PingApiInterface $pingApi,
        CheckPing $checkPing
    ) {
        $this->pingApi   = $pingApi;
        $this->checkPing = $checkPing;
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
            $this->checkPing->checkPing($model);
        }
    }
}
