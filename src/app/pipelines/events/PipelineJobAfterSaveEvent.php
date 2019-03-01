<?php
declare(strict_types=1);

namespace src\app\pipelines\events;

use src\app\pipelines\PipelineApi;
use corbomite\events\interfaces\EventInterface;
use src\app\pipelines\interfaces\PipelineJobModelInterface;

class PipelineJobAfterSaveEvent implements EventInterface
{
    private $new;
    private $model;

    public function __construct(
        PipelineJobModelInterface $model,
        bool $new = false
    ) {
        $this->new = $new;
        $this->model = $model;
    }

    public function new(): bool
    {
        return $this->new;
    }

    public function model(): PipelineJobModelInterface
    {
        return $this->model;
    }

    public function provider(): string
    {
        return PipelineApi::class;
    }

    public function name(): string
    {
        return 'PipelineJobAfterSave';
    }

    private $stop = false;

    public function stopPropagation(?bool $stop = null): bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
