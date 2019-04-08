<?php

declare(strict_types=1);

namespace src\app\pipelines\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\projects\events\ProjectBeforeDeleteEvent;
use function array_walk;

class ProjectDeleteListener implements EventListenerInterface
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

    private function respond(ProjectBeforeDeleteEvent $event) : void
    {
        $queryModel = $this->pipelineApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        $relatedPipelines = $this->pipelineApi->fetchAll();

        array_walk($relatedPipelines, [$this, 'delete']);
    }

    private function delete(PipelineModelInterface $model) : void
    {
        $this->pipelineApi->delete($model);
    }
}
