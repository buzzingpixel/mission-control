<?php
declare(strict_types=1);

namespace src\app\pings\schedules;

use corbomite\queue\interfaces\QueueApiInterface;

class CheckPingsSchedule
{
    private $queueApi;

    public function __construct(QueueApiInterface $queueApi)
    {
        $this->queueApi = $queueApi;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }
}
