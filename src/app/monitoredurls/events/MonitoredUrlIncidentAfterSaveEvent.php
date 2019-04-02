<?php

declare(strict_types=1);

namespace src\app\monitoredurls\events;

use corbomite\events\interfaces\EventInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;
use src\app\monitoredurls\MonitoredUrlsApi;

class MonitoredUrlIncidentAfterSaveEvent implements EventInterface
{
    /** @var bool */
    private $wasNew;
    /** @var MonitoredUrlIncidentModelInterface */
    private $monitoredUrlIncidentModel;

    public function __construct(
        MonitoredUrlIncidentModelInterface $monitoredUrlModel,
        bool $wasNew = false
    ) {
        $this->wasNew                    = $wasNew;
        $this->monitoredUrlIncidentModel = $monitoredUrlModel;
    }

    public function wasNew() : bool
    {
        return $this->wasNew;
    }

    public function monitoredUrlModel() : MonitoredUrlIncidentModelInterface
    {
        return $this->monitoredUrlIncidentModel;
    }

    public function provider() : string
    {
        return MonitoredUrlsApi::class;
    }

    public function name() : string
    {
        return 'MonitoredUrlIncidentAfterSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
