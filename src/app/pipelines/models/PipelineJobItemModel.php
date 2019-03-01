<?php
declare(strict_types=1);

namespace src\app\pipelines\models;

use DateTime;
use corbomite\db\traits\UuidTrait;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\interfaces\PipelineItemModelInterface;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;

class PipelineJobItemModel implements PipelineJobItemModelInterface
{
    use UuidTrait;

    private $pipeline;

    public function pipeline(
        ?PipelineModelInterface $val = null
    ): ?PipelineModelInterface {
        return $this->pipeline = $val ?? $this->pipeline;
    }

    private $pipelineJob;

    public function pipelineJob(
        ?PipelineJobModelInterface $val = null
    ): ?PipelineJobModelInterface {
        return $this->pipelineJob = $val ?? $this->pipelineJob;
    }

    private $pipelineItem;

    public function pipelineItem(
        ?PipelineItemModelInterface $val = null
    ): ?PipelineItemModelInterface {
        return $this->pipelineItem = $val ?? $this->pipelineItem;
    }

    private $hasFailed = false;

    public function hasFailed(?bool $val = null): bool
    {
        return $this->hasFailed = $val ?? $this->hasFailed;
    }

    private $logContent = '';

    public function logContent(?string $val = null): string
    {
        return $this->logContent = $val ?? $this->logContent;
    }

    private $finishedAt;

    public function finishedAt(?DateTime $val = null): ?DateTime
    {
        return $this->finishedAt = $val ?? $this->finishedAt;
    }
}
