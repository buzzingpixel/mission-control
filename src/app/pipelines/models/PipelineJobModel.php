<?php
declare(strict_types=1);

namespace src\app\pipelines\models;

use DateTime;
use DateTimeZone;
use corbomite\db\traits\UuidTrait;
use corbomite\db\models\UuidModel;
use corbomite\db\interfaces\UuidModelInterface;
use src\app\pipelines\interfaces\PipelineJobModelInterface;

class PipelineJobModel implements PipelineJobModelInterface
{
    use UuidTrait;

    private $pipelineUuidModel;

    public function pipelineGuid(?string $guid = null): string
    {
        if ($guid !== null) {
            $this->pipelineUuidModel = new UuidModel($guid);
        }

        if (! $this->pipelineUuidModel) {
            $this->pipelineUuidModel = new UuidModel();
        }

        return $this->pipelineUuidModel->toString();
    }

    public function pipelineGuidAsModel(): UuidModelInterface
    {
        if (! $this->pipelineUuidModel) {
            $this->pipelineUuidModel = new UuidModel();
        }

        return $this->pipelineUuidModel;
    }

    public function getPipelineGuidAsBytes(): string
    {
        if (! $this->pipelineUuidModel) {
            $this->pipelineUuidModel = new UuidModel();
        }

        return $this->pipelineUuidModel->toBytes();
    }

    public function setPipelineGuidAsBytes(string $bytes): void
    {
        $this->pipelineUuidModel = UuidModel::fromBytes($bytes);
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
}
