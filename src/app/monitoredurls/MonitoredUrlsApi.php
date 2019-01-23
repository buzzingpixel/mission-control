<?php
declare(strict_types=1);

namespace src\app\monitoredurls;

use corbomite\di\Di;
use \src\app\support\traits\UuidToBytesTrait;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\monitoredurls\models\MonitoredUrlModel;
use src\app\monitoredurls\services\SaveIncidentService;
use src\app\monitoredurls\services\FetchIncidentsService;
use src\app\monitoredurls\models\MonitoredUrlIncidentModel;
use src\app\monitoredurls\services\SaveMonitoredUrlService;
use src\app\monitoredurls\services\DeleteMonitoredUrlService;
use src\app\monitoredurls\services\FetchMonitoredUrlsService;
use src\app\monitoredurls\services\ArchiveMonitoredUrlService;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\services\UnArchiveMonitoredUrlService;
use src\app\monitoredurls\interfaces\MonitoredUrlIncidentModelInterface;

class MonitoredUrlsApi implements MonitoredUrlsApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(): MonitoredUrlModelInterface
    {
        return new MonitoredUrlModel();
    }

    public function createIncidentModel(): MonitoredUrlIncidentModelInterface
    {
        return new MonitoredUrlIncidentModel();
    }

    public function save(MonitoredUrlModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveMonitoredUrlService::class);
        $service->save($model);
    }

    public function archive(MonitoredUrlModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(ArchiveMonitoredUrlService::class);
        $service->archive($model);
    }

    public function unArchive(MonitoredUrlModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(UnArchiveMonitoredUrlService::class);
        $service->unArchive($model);
    }

    public function delete(MonitoredUrlModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(DeleteMonitoredUrlService::class);
        $service->delete($model);
    }

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?MonitoredUrlModelInterface {
        return $this->fetchAll($params)[0] ?? null;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchMonitoredUrlsService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->addWhere('is_active', '1');
            $params->addOrder('title', 'asc');
        }

        return $service->fetch($params);
    }

    public function saveIncident(MonitoredUrlIncidentModelInterface $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveIncidentService::class);
        $service->save($model);
    }

    public function fetchIncidents(?QueryModelInterface $params = null): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchIncidentsService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->limit(50);
            $params->addOrder('event_at');
        }

        return $service->fetch($params);
    }
}
