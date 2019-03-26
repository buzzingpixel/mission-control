<?php
declare(strict_types=1);

namespace src\app\pipelines\services;

use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;

class InitJobFromPipelineModelService
{
    private $pipelineApi;

    public function __construct(PipelineApiInterface $pipelineApi)
    {
        $this->pipelineApi = $pipelineApi;
    }

    public function __invoke(PipelineModelInterface $pipeline): void
    {
        $this->init($pipeline);
    }

    public function init(PipelineModelInterface $pipeline): void
    {
        $job = $this->pipelineApi->createPipelineJobModel();

        $job->pipeline($pipeline);

        foreach ($pipeline->pipelineItems() as $item) {
            $jobItem = $this->pipelineApi->createPipelineJobItemModel();
            $jobItem->pipeline($pipeline);
            $jobItem->pipelineJob($job);
            $jobItem->pipelineItem($item);
            $job->addPipelineJobItem($jobItem);
        }

        $this->pipelineApi->saveJob($job);
    }
}
