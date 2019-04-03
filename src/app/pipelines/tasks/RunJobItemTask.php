<?php

declare(strict_types=1);

namespace src\app\pipelines\tasks;

use src\app\pipelines\interfaces\PipelineApiInterface;
use function dd;

class RunJobItemTask
{
    /** @var PipelineApiInterface */
    private $pipelineApi;

    public function __construct(PipelineApiInterface $pipelineApi)
    {
        $this->pipelineApi = $pipelineApi;
    }

    /**
     * @param mixed[] $context
     */
    public function __invoke(array $context = []) : void
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
