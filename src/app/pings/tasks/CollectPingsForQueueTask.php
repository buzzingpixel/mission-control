<?php
declare(strict_types=1);

namespace src\app\pings\tasks;

use src\app\pings\interfaces\PingApiInterface;
use corbomite\queue\interfaces\QueueApiInterface;

class CollectPingsForQueueTask
{
    public const BATCH_NAME = 'collectPingsForQueue';
    public const BATCH_TITLE = 'Collect Pings for Queue';

    private $pingApi;
    private $queueApi;

    public function __construct(
        PingApiInterface $pingApi,
        QueueApiInterface $queueApi
    ) {
        $this->pingApi = $pingApi;
        $this->queueApi = $queueApi;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }
}
