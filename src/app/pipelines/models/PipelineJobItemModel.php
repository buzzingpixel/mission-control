<?php
declare(strict_types=1);

namespace src\app\pipelines\models;

use DateTime;
use corbomite\db\traits\UuidTrait;
use corbomite\db\models\UuidModel;
use corbomite\db\interfaces\UuidModelInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;

class PipelineJobItemModel implements PipelineJobItemModelInterface
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

    private $pipelineJobUuidModel;

    public function pipelineJobGuid(?string $guid = null): string
    {
        if ($guid !== null) {
            $this->pipelineJobUuidModel = new UuidModel($guid);
        }

        if (! $this->pipelineJobUuidModel) {
            $this->pipelineJobUuidModel = new UuidModel();
        }

        return $this->pipelineJobUuidModel->toString();
    }

    public function pipelineJobGuidAsModel(): UuidModelInterface
    {
        if (! $this->pipelineJobUuidModel) {
            $this->pipelineJobUuidModel = new UuidModel();
        }

        return $this->pipelineJobUuidModel;
    }

    public function getPipelineJobGuidAsBytes(): string
    {
        if (! $this->pipelineJobUuidModel) {
            $this->pipelineJobUuidModel = new UuidModel();
        }

        return $this->pipelineJobUuidModel->toBytes();
    }

    public function setPipelineJobGuidAsBytes(string $bytes): void
    {
        $this->pipelineJobUuidModel = UuidModel::fromBytes($bytes);
    }

    private $pipelineItemUuidModel;

    public function pipelineItemGuid(?string $guid = null): string
    {
        if ($guid !== null) {
            $this->pipelineItemUuidModel = new UuidModel($guid);
        }

        if (! $this->pipelineItemUuidModel) {
            $this->pipelineItemUuidModel = new UuidModel();
        }

        return $this->pipelineItemUuidModel->toString();
    }

    public function pipelineItemGuidAsModel(): UuidModelInterface
    {
        if (! $this->pipelineItemUuidModel) {
            $this->pipelineItemUuidModel = new UuidModel();
        }

        return $this->pipelineItemUuidModel;
    }

    public function getPipelineItemGuidAsBytes(): string
    {
        if (! $this->pipelineItemUuidModel) {
            $this->pipelineItemUuidModel = new UuidModel();
        }

        return $this->pipelineItemUuidModel->toBytes();
    }

    public function setPipelineItemGuidAsBytes(string $bytes): void
    {
        $this->pipelineItemUuidModel = UuidModel::fromBytes($bytes);
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
