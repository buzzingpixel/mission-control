<?php

declare(strict_types=1);

namespace src\app\monitoredurls\events;

use corbomite\events\interfaces\EventInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;
use src\app\monitoredurls\MonitoredUrlsApi;

class MonitoredUrlIncidentBeforeSaveEvent implements EventInterface
{
    /** @var bool */
    private $isNew;
    /** @var MonitoredUrlIncidentModelInterface */
    private $monitoredUrlIncidentModel;

    public function __construct(
        MonitoredUrlIncidentModelInterface $monitoredUrlIncidentModel,
        bool $isNew = false
    ) {
        $this->isNew                     = $isNew;
        $this->monitoredUrlIncidentModel = $monitoredUrlIncidentModel;
    }

    public function isNew() : bool
    {
        return $this->isNew;
    }

    public function monitoredUrlIncidentModel() : MonitoredUrlIncidentModelInterface
    {
        return $this->monitoredUrlIncidentModel;
    }

    public function provider() : string
    {
        return MonitoredUrlsApi::class;
    }

    public function name() : string
    {
        return 'MonitoredUrlIncidentBeforeSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
