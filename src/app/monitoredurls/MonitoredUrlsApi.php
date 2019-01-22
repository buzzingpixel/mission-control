<?php
declare(strict_types=1);

namespace src\app\monitoredurls;

use corbomite\di\Di;
use corbomite\db\Factory as DbFactory;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\monitoredurls\models\MonitoredUrlIncidentModel;
use src\app\monitoredurls\models\MonitoredUrlModel;
use src\app\monitoredurls\services\SaveIncidentService;
use src\app\monitoredurls\services\FetchIncidentsService;
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
    private $di;
    private $dbFactory;

    public function __construct(Di $di, DbFactory $dbFactory)
    {
        $this->di = $di;
        $this->dbFactory = $dbFactory;
    }

    public function createModel(array $props = []): MonitoredUrlModelInterface
    {
        return new MonitoredUrlModel($props);
    }

    public function createIncidentModel(array $props = []): MonitoredUrlIncidentModelInterface
    {
        return new MonitoredUrlIncidentModel($props);
    }

    public function makeQueryModel(): QueryModelInterface
    {
        return $this->dbFactory->makeQueryModel();
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
            $params->addOrder('event_at', 'desc');
        }

        return $service->fetch($params);
    }
}
