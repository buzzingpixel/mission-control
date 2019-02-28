<?php
declare(strict_types=1);

namespace src\app\pipelines\events;

use src\app\pipelines\PipelineApi;
use corbomite\events\interfaces\EventInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;

class PipelineAfterSaveEvent implements EventInterface
{
    private $new;
    private $model;

    public function __construct(
        PipelineModelInterface $model,
        bool $new = false
    ) {
        $this->new = $new;
        $this->model = $model;
    }

    public function new(): bool
    {
        return $this->new;
    }

    public function model(): PipelineModelInterface
    {
        return $this->model;
    }

    public function provider(): string
    {
        return PipelineApi::class;
    }

    public function name(): string
    {
        return 'PipelineAfterSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
