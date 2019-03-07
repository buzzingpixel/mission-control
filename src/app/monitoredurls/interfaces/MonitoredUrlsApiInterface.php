<?php
declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlModelException;
use src\app\monitoredurls\exceptions\MonitoredUrlNameNotUniqueException;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlIncidentModelException;

interface MonitoredUrlsApiInterface
{
    /**
     * Creates a Monitored URL model
     * @return MonitoredUrlModelInterface
     */
    public function createModel(): MonitoredUrlModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     * @param string $string
     * @return string
     */
    public function uuidToBytes(string $string): string;

    /**
     * Creates a Monitored URL Incident Model
     * @param array $props
     * @return MonitoredUrlIncidentModelInterface
     */
    public function createIncidentModel(): MonitoredUrlIncidentModelInterface;

    /**
     * Creates a Fetch Data Params instance
     * @return QueryModelInterface
     */
    public function makeQueryModel(): QueryModelInterface;

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
     * @param QueryModelInterface $params
     * @return MonitoredUrlModelInterface|null
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?MonitoredUrlModelInterface;

    /**
     * Fetches all Monitored URL models based on params
     * @param QueryModelInterface $params
     * @return MonitoredUrlModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null): array;

    /**
     * Saves an incident
     * @param MonitoredUrlIncidentModelInterface $model
     * @throws InvalidMonitoredUrlIncidentModelException
     */
    public function saveIncident(MonitoredUrlIncidentModelInterface $model);

    /**
     * Fetches one incident base on params
     * @param QueryModelInterface|null $params
     * @return MonitoredUrlIncidentModelInterface|null
     */
    public function fetchOneIncident(?QueryModelInterface $params = null): ?MonitoredUrlIncidentModelInterface;

    /**
     * Fetches incidents based on params
     * @return MonitoredUrlIncidentModelInterface[]
     */
    public function fetchIncidents(?QueryModelInterface $params = null): array;
}
