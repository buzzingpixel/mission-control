<?php

declare(strict_types=1);

namespace src\app\monitoredurls\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlIncidentModelException;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlModelException;
use src\app\monitoredurls\exceptions\MonitoredUrlNameNotUniqueException;

interface MonitoredUrlsApiInterface
{
    /**
     * Creates a Monitored URL model
     */
    public function createModel() : MonitoredUrlModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     */
    public function uuidToBytes(string $string) : string;

    /**
     * Creates a Monitored URL Incident Model
     */
    public function createIncidentModel() : MonitoredUrlIncidentModelInterface;

    /**
     * Creates a Fetch Data Params instance
     */
    public function makeQueryModel() : QueryModelInterface;

    /**
     * Saves a monitored URL
     *
     * @throws InvalidMonitoredUrlModelException
     * @throws MonitoredUrlNameNotUniqueException
     */
    public function save(MonitoredUrlModelInterface $model) : void;

    /**
     * Archives a monitored URL
     */
    public function archive(MonitoredUrlModelInterface $model) : void;

    /**
     * Un-archives a monitored URL
     */
    public function unArchive(MonitoredUrlModelInterface $model) : void;

    /**
     * Deletes a monitored URL
     *
     * @return mixed
     */
    public function delete(MonitoredUrlModelInterface $model);

    /**
     * Fetches one Monitored URL model result based on params
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?MonitoredUrlModelInterface;

    /**
     * Fetches all Monitored URL models based on params
     *
     * @return MonitoredUrlModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array;

    /**
     * Saves an incident
     *
     * @throws InvalidMonitoredUrlIncidentModelException
     */
    public function saveIncident(MonitoredUrlIncidentModelInterface $model) : void;

    /**
     * Fetches one incident base on params
     */
    public function fetchOneIncident(?QueryModelInterface $params = null) : ?MonitoredUrlIncidentModelInterface;

    /**
     * Fetches incidents based on params
     *
     * @return MonitoredUrlIncidentModelInterface[]
     */
    public function fetchIncidents(?QueryModelInterface $params = null) : array;
}
