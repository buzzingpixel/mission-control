<?php
declare(strict_types=1);

namespace src\app\pings;

use src\app\pings\models\PingModel;
use Psr\Container\ContainerInterface;
use src\app\pings\services\SavePingService;
use src\app\support\traits\UuidToBytesTrait;
use src\app\pings\services\FetchPingService;
use src\app\pings\services\DeletePingService;
use src\app\pings\interfaces\PingApiInterface;
use src\app\pings\services\ArchivePingService;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\pings\interfaces\PingModelInterface;
use src\app\pings\services\UnArchivePingService;

class PingApi implements PingApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    public function createModel(): PingModelInterface
    {
        return new PingModel();
    }

    public function save(PingModelInterface $model): void
    {
        $service = $this->di->get(SavePingService::class);
        $service->save($model);
    }

    public function archive(PingModelInterface $model): void
    {
        $service = $this->di->get(ArchivePingService::class);
        $service->archive($model);
    }

    public function unArchive(PingModelInterface $model): void
    {
        $service = $this->di->get(UnArchivePingService::class);
        $service->unArchive($model);
    }

    public function delete(PingModelInterface $model)
    {
        $service = $this->di->get(DeletePingService::class);
        $service->delete($model);
    }

    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?PingModelInterface {
        $this->limit = 1;
        $result = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;
        return $result;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        $service = $this->di->get(FetchPingService::class);

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
}
