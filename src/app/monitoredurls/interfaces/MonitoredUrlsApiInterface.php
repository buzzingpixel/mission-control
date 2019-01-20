<?php
declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use src\app\datasupport\FetchDataParamsInterface;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlModelException;
use src\app\monitoredurls\exceptions\MonitoredUrlNameNotUniqueException;

interface MonitoredUrlsApiInterface
{
    /**
     * Creates a Monitored URL model
     * @param array $props
     * @return MonitoredUrlModelInterface
     */
    public function createModel(array $props = []): MonitoredUrlModelInterface;

    /**
     * Creates a Fetch Data Params instance
     * @return FetchDataParamsInterface
     */
    public function createFetchDataParams(): FetchDataParamsInterface;

    /**
     * Saves a monitored URL
     * @param MonitoredUrlModelInterface $model
     * @throws InvalidMonitoredUrlModelException
     * @throws MonitoredUrlNameNotUniqueException
     */
    public function save(MonitoredUrlModelInterface $model);

    /**
     * Archives a monitored URL
     * @param MonitoredUrlModelInterface $model
     */
    public function archive(MonitoredUrlModelInterface $model);

    /**
     * Un-archives a monitored URL
     * @param MonitoredUrlModelInterface $model
     */
    public function unArchive(MonitoredUrlModelInterface $model);

    /**
     * Deletes a monitored URL
     * @param MonitoredUrlModelInterface $model
     * @return mixed
     */
    public function delete(MonitoredUrlModelInterface $model);

    /**
     * Fetches one Monitored URL model result based on params
     * @param FetchDataParamsInterface $params
     * @return MonitoredUrlModelInterface|null
     */
    public function fetchOne(
        ?FetchDataParamsInterface $params = null
    ): ?MonitoredUrlModelInterface;

    /**
     * Fetches all Monitored URL models based on params
     * @param FetchDataParamsInterface $params
     * @return MonitoredUrlModelInterface[]
     */
    public function fetchAll(?FetchDataParamsInterface $params = null): array;
}
