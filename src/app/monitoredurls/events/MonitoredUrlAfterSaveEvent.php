<?php
declare(strict_types=1);

namespace src\app\monitoredurls\events;

use src\app\monitoredurls\MonitoredUrlsApi;
use corbomite\events\interfaces\EventInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class MonitoredUrlAfterSaveEvent implements EventInterface
{
    private $isNew;
    private $monitoredUrlModel;

    public function __construct(
        MonitoredUrlModelInterface $monitoredUrlModel,
        bool $isNew = false
    ) {
        $this->isNew = $isNew;
        $this->monitoredUrlModel = $monitoredUrlModel;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function monitoredUrlModel(): MonitoredUrlModelInterface
    {
        return $this->monitoredUrlModel;
    }

    public function provider(): string
    {
        return MonitoredUrlsApi::class;
    }

    public function name(): string
    {
        return 'MonitoredUrlAfterSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
