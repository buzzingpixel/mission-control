<?php

declare(strict_types=1);

namespace src\app\pipelines;

use corbomite\db\interfaces\QueryModelInterface;
use Psr\Container\ContainerInterface;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\interfaces\PipelineItemModelInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\models\PipelineItemModel;
use src\app\pipelines\models\PipelineJobItemModel;
use src\app\pipelines\models\PipelineJobModel;
use src\app\pipelines\models\PipelineModel;
use src\app\pipelines\services\ArchivePipelineService;
use src\app\pipelines\services\DeletePipelineService;
use src\app\pipelines\services\FetchOnePipelineJobItemService;
use src\app\pipelines\services\FetchPipelineJobService;
use src\app\pipelines\services\FetchPipelineService;
use src\app\pipelines\services\InitJobFromPipelineModelService;
use src\app\pipelines\services\SavePipelineJobService;
use src\app\pipelines\services\SavePipelineService;
use src\app\pipelines\services\UnArchivePipelineService;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\support\traits\UuidToBytesTrait;

class PipelineApi implements PipelineApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createPipelineModel() : PipelineModelInterface
    {
        return new PipelineModel();
    }

    public function createPipelineItemModel() : PipelineItemModelInterface
    {
        return new PipelineItemModel();
    }

    public function createPipelineJobModel() : PipelineJobModelInterface
    {
        return new PipelineJobModel();
    }

    public function createPipelineJobItemModel() : PipelineJobItemModelInterface
    {
        return new PipelineJobItemModel();
    }

    public function initJobFromPipelineModel(PipelineModelInterface $pipelineModel) : void
    {
        $service = $this->di->get(InitJobFromPipelineModelService::class);
        $service->init($pipelineModel);
    }

    public function save(PipelineModelInterface $model) : void
    {
        $service = $this->di->get(SavePipelineService::class);
        $service->save($model);
    }

    public function saveJob(PipelineJobModelInterface $model) : void
    {
        $service = $this->di->get(SavePipelineJobService::class);
        $service->save($model);
    }

    public function archive(PipelineModelInterface $model) : void
    {
        $service = $this->di->get(ArchivePipelineService::class);
        $service->archive($model);
    }

    public function unArchive(PipelineModelInterface $model) : void
    {
        $service = $this->di->get(UnArchivePipelineService::class);
        $service->unArchive($model);
    }

    public function delete(PipelineModelInterface $model) : void
    {
        $service = $this->di->get(DeletePipelineService::class);
        $service->delete($model);
    }

    /** @var ?int */
    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?PipelineModelInterface {
        $this->limit = 1;
        $result      = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;

        return $result;
    }

    /**
     * @return PipelineModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array
    {
        $service = $this->di->get(FetchPipelineService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        if ($this->limit) {
            $params->limit($this->limit);
        }

        return $service->fetch($params);
    }

    /** @var ?int */
    private $jobLimit;

    public function fetchOneJob(
        ?QueryModelInterface $params = null
    ) : ?PipelineJobModelInterface {
        $this->jobLimit = 1;
        $result         = $this->fetchAllJobs($params)[0] ?? null;
        $this->jobLimit = null;

        return $result;
    }

    /**
     * @return PipelineJobModelInterface[]
     */
    public function fetchAllJobs(?QueryModelInterface $params = null) : array
    {
        $service = $this->di->get(FetchPipelineJobService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
        }

        if ($this->jobLimit) {
            $params->limit($this->jobLimit);
        }

        return $service->fetch($params);
    }

    public function fetchOneJobItem(?QueryModelInterface $params = null) : ?PipelineJobItemModelInterface
    {
        $service = $this->di->get(FetchOnePipelineJobItemService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
        }

        return $service->fetch($params);
    }
}
