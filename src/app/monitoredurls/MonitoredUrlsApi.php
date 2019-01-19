<?php
declare(strict_types=1);

namespace src\app\monitoredurls;

use corbomite\di\Di;
use src\app\datasupport\FetchDataParamsFactory;
use src\app\datasupport\FetchDataParamsInterface;
use src\app\monitoredurls\models\MonitoredUrlModel;
use src\app\monitoredurls\services\SaveMonitoredUrlService;
use src\app\monitoredurls\services\FetchMonitoredUrlsService;
use src\app\monitoredurls\services\ArchiveMonitoredUrlService;
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\services\UnArchiveMonitoredUrlService;

class MonitoredUrlsApi implements MonitoredUrlsApiInterface
{
    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(array $props = []): MonitoredUrlModelInterface
    {
        return new MonitoredUrlModel($props);
    }

    public function createFetchDataParams(): FetchDataParamsInterface
    {
        return (new FetchDataParamsFactory())->make();
    }

    public function save(MonitoredUrlModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveMonitoredUrlService::class);
        $service->save($model);
    }

    public function archive(MonitoredUrlModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(ArchiveMonitoredUrlService::class);
        $service->archive($model);
    }

    public function unArchive(MonitoredUrlModelInterface $model)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(UnArchiveMonitoredUrlService::class);
        $service->unArchive($model);
    }

    public function delete(MonitoredUrlModelInterface $model)
    {
        // TODO: Implement service method to execute this interface method
    }

    public function fetchOne(
        FetchDataParamsInterface $params
    ): ?MonitoredUrlModelInterface {
        return $this->fetchAll($params)[0] ?? null;
    }

    public function fetchAll(FetchDataParamsInterface $params): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(FetchMonitoredUrlsService::class);
        return $service->fetch($params);
    }
}
