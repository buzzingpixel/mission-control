<?php
declare(strict_types=1);

namespace src\app\pipelines\tasks;

use src\app\pipelines\interfaces\PipelineApiInterface;

class RunJobItemTask
{
    private $pipelineApi;

    public function __construct(PipelineApiInterface $pipelineApi)
    {
        $this->pipelineApi = $pipelineApi;
    }

    public function __invoke(array $context = [])
    {
        $jobItemGuid = $context['jobItemGuid'] ?? '';

        if (! $jobItemGuid) {
            return;
        }

        $queryModel = $this->pipelineApi->makeQueryModel();
        $queryModel->addWhere('guid', $this->pipelineApi->uuidToBytes($jobItemGuid));
        $jobItem = $this->pipelineApi->fetchOneJobItem($queryModel);

        // TODO: Run Pipeline Item
        dd($jobItem->pipelineItem());
    }
}
