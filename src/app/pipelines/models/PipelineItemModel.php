<?php
declare(strict_types=1);

namespace src\app\pipelines\models;

use corbomite\db\traits\UuidTrait;
use corbomite\db\models\UuidModel;
use corbomite\db\interfaces\UuidModelInterface;
use src\app\pipelines\interfaces\PipelineItemModelInterface;

class PipelineItemModel implements PipelineItemModelInterface
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

    private $script = '';

    public function script(?string $val = null): string
    {
        return $this->script = $val ?? $this->script;
    }
}
