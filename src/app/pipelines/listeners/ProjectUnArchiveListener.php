<?php

declare(strict_types=1);

namespace src\app\pipelines\listeners;

use corbomite\events\interfaces\EventInterface;
use corbomite\events\interfaces\EventListenerInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\projects\events\ProjectBeforeUnArchiveEvent;
use Throwable;
use function array_walk;

class ProjectUnArchiveListener implements EventListenerInterface
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

    private function respond(ProjectBeforeUnArchiveEvent $event) : void
    {
        $queryModel = $this->pipelineApi->makeQueryModel();
        $queryModel->addWhere('project_guid', $event->projectModel()->getGuidAsBytes());

        $relatedPipelines = $this->pipelineApi->fetchAll($queryModel);

        array_walk($relatedPipelines, [$this, 'unArchive']);
    }

    /**
     * @throws Throwable
     */
    private function unArchive(PipelineModelInterface $model) : void
    {
        if ($model->isActive()) {
            return;
        }

        $model->isActive(true);

        $this->pipelineApi->save($model);
    }
}
