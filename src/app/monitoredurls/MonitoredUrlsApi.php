<?php
declare(strict_types=1);

namespace src\app\monitoredurls;

use Psr\Container\ContainerInterface;
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

    public function __construct(ContainerInterface $di)
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
        $service = $this->di->get(SaveMonitoredUrlService::class);
        $service->save($model);
    }

    public function archive(MonitoredUrlModelInterface $model): void
    {
        $service = $this->di->get(ArchiveMonitoredUrlService::class);
        $service->archive($model);
    }

    public function unArchive(MonitoredUrlModelInterface $model): void
    {
        $service = $this->di->get(UnArchiveMonitoredUrlService::class);
        $service->unArchive($model);
    }

    public function delete(MonitoredUrlModelInterface $model)
    {
        $service = $this->di->get(DeleteMonitoredUrlService::class);
        $service->delete($model);
    }

    private $limit;

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?MonitoredUrlModelInterface {
        $this->limit = 1;
        $all = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;
        return $all;
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        $service = $this->di->get(FetchMonitoredUrlsService::class);

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

    public function saveIncident(MonitoredUrlIncidentModelInterface $model): void
    {
        $service = $this->di->get(SaveIncidentService::class);
        $service->save($model);
    }

    private $incidentLimit;

    public function fetchOneIncident(?QueryModelInterface $params = null): ?MonitoredUrlIncidentModelInterface
    {
        $this->incidentLimit = 1;
        $all = $this->fetchIncidents($params)[0] ?? null;
        $this->incidentLimit = null;
        return $all;
    }

    public function fetchIncidents(?QueryModelInterface $params = null): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->get(FetchIncidentsService::class);

        if (! $params) {
            $params = $this->makeQueryModel();
            $params->limit(50);
            $params->addOrder('event_at');
        }

        if ($this->incidentLimit) {
            $params->limit($this->incidentLimit);
        }

        return $service->fetch($params);
    }
}
