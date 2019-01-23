<?php
declare(strict_types=1);

namespace src\app\pings;

use corbomite\di\Di;
use src\app\pings\models\PingModel;
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

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(): PingModelInterface
    {
        return new PingModel();
    }

    public function save(PingModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SavePingService::class);
        $service->save($model);
    }

    public function archive(PingModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(ArchivePingService::class);
        $service->archive($model);
    }

    public function unArchive(PingModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(UnArchivePingService::class);
        $service->unArchive($model);
    }

    public function delete(PingModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DeletePingService::class);
        $service->delete($model);
    }

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?PingModelInterface {
        return $this->fetchAll($params)[0] ?? null;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchPingService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        return $service->fetch($params);
    }
}
