<?php

declare(strict_types=1);

namespace src\app\pipelines\models;

use src\app\pipelines\interfaces\PipelineItemModelInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\support\traits\StandardModelTrait;

class PipelineModel implements PipelineModelInterface
{
    use StandardModelTrait;

    /** @var string */
    private $description = '';

    public function description(?string $val = null) : string
    {
        return $this->description = $val ?? $this->description;
    }

    /** @var string */
    private $secretId = '';

    public function secretId(?string $val = null) : string
    {
        return $this->secretId = $val ?? $this->secretId;
    }

    /** @var string */
    private $runBeforeEveryItem = '';

    public function runBeforeEveryItem(?string $val = null) : string
    {
        return $this->runBeforeEveryItem = $val ?? $this->runBeforeEveryItem;
    }

    /** @var PipelineItemModelInterface[] */
    private $pipelineItems = [];

    /**
     * @param PipelineItemModelInterface[] $val
     *
     * @return PipelineItemModelInterface[]
     */
    public function pipelineItems(?array $val = null) : array
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

    public function addPipelineItem(PipelineItemModelInterface $model) : void
    {
        $this->pipelineItems[] = $model;
    }
}
