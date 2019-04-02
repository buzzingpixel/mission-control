<?php

declare(strict_types=1);

namespace src\app\monitoredurls\events;

use corbomite\events\interfaces\EventInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\MonitoredUrlsApi;

class MonitoredUrlAfterUnArchiveEvent implements EventInterface
{
    /** @var MonitoredUrlModelInterface */
    private $monitoredUrlModel;

    public function __construct(
        MonitoredUrlModelInterface $monitoredUrlModel
    ) {
        $this->monitoredUrlModel = $monitoredUrlModel;
    }

    public function monitoredUrlModel() : MonitoredUrlModelInterface
    {
        return $this->monitoredUrlModel;
    }

    public function provider() : string
    {
        return MonitoredUrlsApi::class;
    }

    public function name() : string
    {
        return 'MonitoredUrlAfterUnArchive';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
