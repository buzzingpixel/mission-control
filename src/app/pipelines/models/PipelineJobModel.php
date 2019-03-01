<?php
declare(strict_types=1);

namespace src\app\pipelines\models;

use DateTime;
use DateTimeZone;
use corbomite\db\traits\UuidTrait;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;

class PipelineJobModel implements PipelineJobModelInterface
{
    use UuidTrait;

    private $pipeline;

    public function pipeline(
        ?PipelineModelInterface $val = null
    ): ?PipelineModelInterface {
        return $this->pipeline = $val ?? $this->pipeline;
    }

    private $hasStarted = false;

    public function hasStarted(?bool $val = null): bool
    {
        return $this->hasStarted = $val ?? $this->hasStarted;
    }

    private $isFinished = false;

    public function isFinished(?bool $val = null): bool
    {
        return $this->isFinished = $val ?? $this->isFinished;
    }

    private $hasFailed = false;

    public function hasFailed(?bool $val = null): bool
    {
        return $this->hasFailed = $val ?? $this->hasFailed;
    }

    private $percentComplete = 0.0;

    public function percentComplete(?float $val = null): float
    {
        return $this->percentComplete = $val ?? $this->percentComplete;
    }

    /** @var DateTime|null */
    private $jobAddedAt;

    public function jobAddedAt(?DateTime $val = null): DateTime
    {
        if (! $val && ! $this->jobAddedAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->jobAddedAt = new DateTime('now', new DateTimeZone('UTC'));
        }

        return $this->jobAddedAt = $val ?? $this->jobAddedAt;
    }

    private $jobFinishedAt;

    public function jobFinishedAt(?DateTime $val = null): ?DateTime
    {
        return $this->jobFinishedAt = $val ?? $this->jobFinishedAt;
    }

    private $pipelineJobItems = [];

    public function pipelineJobItems(?array $val = null): array
    {
        if ($val === null) {
            return $this->pipelineJobItems;
        }

        $this->pipelineJobItems = [];

        foreach ($val as $model) {
            $this->addPipelineJobItem($model);
        }

        return $this->pipelineJobItems;
    }

    public function addPipelineJobItem(PipelineJobItemModelInterface $model)
    {
        $this->pipelineJobItems[] = $model;
    }
}
