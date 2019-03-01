<?php
declare(strict_types=1);

namespace src\app\pipelines;

use Psr\Container\ContainerInterface;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\models\PipelineModel;
use src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\pipelines\models\PipelineItemModel;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\pipelines\services\SavePipelineService;
use src\app\pipelines\services\FetchPipelineService;
use src\app\pipelines\services\DeletePipelineService;
use src\app\pipelines\interfaces\PipelineApiInterface;
use src\app\pipelines\services\ArchivePipelineService;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\pipelines\services\UnArchivePipelineService;
use src\app\pipelines\interfaces\PipelineItemModelInterface;

class PipelineApi implements PipelineApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createPipelineModel(): PipelineModelInterface
    {
        return new PipelineModel();
    }

    public function createPipelineItemModel(): PipelineItemModelInterface
    {
        return new PipelineItemModel();
    }

    public function save(PipelineModelInterface $model): void
    {
        $service = $this->di->get(SavePipelineService::class);
        $service->save($model);
    }

    public function saveJob(PipelineJobModelInterface $model): void
    {
        // TODO: Implement saveJob() method.
    }

    public function archive(PipelineModelInterface $model): void
    {
        $service = $this->di->get(ArchivePipelineService::class);
        $service->archive($model);
    }

    public function unArchive(PipelineModelInterface $model): void
    {
        $service = $this->di->get(UnArchivePipelineService::class);
        $service->unArchive($model);
    }

    public function delete(PipelineModelInterface $model): void
    {
        $service = $this->di->get(DeletePipelineService::class);
        $service->delete($model);
    }

    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?PipelineModelInterface {
        $this->limit = 1;
        $result = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;
        return $result;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
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

    public function fetchOneJob(
        ?QueryModelInterface $params = null
    ): ?PipelineJobModelInterface {
        // TODO: Implement fetchOneJob() method.
    }

    public function fetchAllJobs(?QueryModelInterface $params = null): array
    {
        // TODO: Implement fetchAllJobs() method.
    }
}
