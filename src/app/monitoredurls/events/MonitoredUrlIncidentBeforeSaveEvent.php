<?php
declare(strict_types=1);

namespace src\app\monitoredurls\events;

use src\app\monitoredurls\MonitoredUrlsApi;
use corbomite\events\interfaces\EventInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;

class MonitoredUrlIncidentBeforeSaveEvent implements EventInterface
{
    private $isNew;
    private $monitoredUrlIncidentModel;

    public function __construct(
        MonitoredUrlIncidentModelInterface $monitoredUrlIncidentModel,
        bool $isNew = false
    ) {
        $this->isNew = $isNew;
        $this->monitoredUrlIncidentModel = $monitoredUrlIncidentModel;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function monitoredUrlIncidentModel(): MonitoredUrlIncidentModelInterface
    {
        return $this->monitoredUrlIncidentModel;
    }

    public function provider(): string
    {
        return MonitoredUrlsApi::class;
    }

    public function name(): string
    {
        return 'MonitoredUrlIncidentBeforeSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
