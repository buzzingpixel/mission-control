<?php
declare(strict_types=1);

namespace src\app\pipelines\models;

use corbomite\db\traits\UuidTrait;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\interfaces\PipelineItemModelInterface;

class PipelineItemModel implements PipelineItemModelInterface
{
    use UuidTrait;

    private $pipeline;

    public function pipeline(
        ?PipelineModelInterface $val = null
    ): ?PipelineModelInterface {
        return $this->pipeline = $val ?? $this->pipeline;
    }

    private $description = '';

    public function description(?string $val = null): string
    {
        return $this->description = $val ?? $this->description;
    }

    private $script = '';

    public function script(?string $val = null): string
    {
        return $this->script = $val ?? $this->script;
    }
}
