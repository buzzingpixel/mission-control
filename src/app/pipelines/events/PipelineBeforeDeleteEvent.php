<?php

declare(strict_types=1);

namespace src\app\pipelines\events;

use corbomite\events\interfaces\EventInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\PipelineApi;

class PipelineBeforeDeleteEvent implements EventInterface
{
    /** @var PipelineModelInterface */
    private $model;

    public function __construct(PipelineModelInterface $model)
    {
        $this->model = $model;
    }

    public function model() : PipelineModelInterface
    {
        return $this->model;
    }

    public function provider() : string
    {
        return PipelineApi::class;
    }

    public function name() : string
    {
        return 'PipelineBeforeDelete';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
