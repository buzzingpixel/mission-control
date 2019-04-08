<?php

declare(strict_types=1);

namespace src\app\pipelines\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\projects\events\ProjectBeforeArchiveEvent;
use Throwable;
use function array_walk;

class ProjectArchiveListener implements EventListenerInterface
{
    /** @var PipelineApiInterface */
    private $pipelineApi;

    public function __construct(PipelineApiInterface $pipelineApi)
    {
        $this->pipelineApi = $pipelineApi;
    }

    public function call(EventInterface $event) : void
    {
        /** @noinspection PhpParamsInspection */
        $this->respond($event);
    }

    private function respond(ProjectBeforeArchiveEvent $event) : void
    {
        $queryModel = $this->pipelineApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        $relatedPipelines = $this->pipelineApi->fetchAll();

        array_walk($relatedPipelines, [$this, 'archive']);
    }

    /**
     * @throws Throwable
     */
    private function archive(PipelineModelInterface $model) : void
    {
        if (! $model->isActive()) {
            return;
        }

        $model->isActive(false);

        $this->pipelineApi->save($model);
    }
}
