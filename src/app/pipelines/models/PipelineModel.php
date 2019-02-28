<?php
declare(strict_types=1);

namespace src\app\pipelines\models;

use src\app\support\traits\StandardModelTrait;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\interfaces\PipelineItemModelInterface;

class PipelineModel implements PipelineModelInterface
{
    use StandardModelTrait;

    private $description = '';

    public function description(?string $val = null): string
    {
        return $this->description = $val ?? $this->description;
    }

    private $secretId = '';

    public function secretId(?string $val = null): string
    {
        return $this->secretId = $val ?? $this->secretId;
    }

    private $pipelineItems = [];

    public function pipelineItems(?array $val = null): array
    {
        if ($val === null) {
            return $this->pipelineItems;
        }

        $this->pipelineItems = [];

        foreach ($val as $model) {
            $this->addPipelineItem($model);
        }

        return $this->pipelineItems;
    }

    public function addPipelineItem(PipelineItemModelInterface $model)
    {
        $this->pipelineItems[] = $model;
    }
}
