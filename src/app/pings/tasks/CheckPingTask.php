<?php
declare(strict_types=1);

namespace src\app\pings\tasks;

use src\app\pings\interfaces\PingApiInterface;
use buzzingpixel\corbomitemailer\interfaces\EmailApiInterface;

class CheckPingTask
{
    public const BATCH_NAME = 'checkPings';
    public const BATCH_TITLE = 'Check Pings';

    private $pingApi;
    private $emailApi;

    public function __construct(
        PingApiInterface $pingApi,
        EmailApiInterface $emailApi
    ) {
        $this->pingApi = $pingApi;
        $this->emailApi = $emailApi;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }
}
