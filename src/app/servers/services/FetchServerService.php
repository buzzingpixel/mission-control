<?php
declare(strict_types=1);

namespace src\app\servers\services;

use Psr\Container\ContainerInterface;
use src\app\data\Server\Server;
use src\app\data\Server\ServerRecord;
use corbomite\db\Factory as OrmFactory;
use src\app\servers\models\ServerModel;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\servers\interfaces\ServerModelInterface;

class FetchServerService
{
    private $di;
    private $ormFactory;
    private $buildQuery;
    private $fetchSSHKey;

    public function __construct(
        ContainerInterface $di,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        FetchSSHKeyService $fetchSSHKey
    ) {
        $this->di = $di;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->fetchSSHKey = $fetchSSHKey;
    }

    /**
     * @return ServerModelInterface[]
     */
    public function __invoke(QueryModelInterface $params): array
    {
        return $this->fetch($params);
    }

    /**
     * @return ServerModelInterface[]
     */
    public function fetch(QueryModelInterface $params): array
    {
        $models = [];

        $results = $this->fetchResults($params);

        $sshKeyIds = [];

        foreach ($results as $record) {
            $sshKeyIds[] = $record->ssh_key_guid;
        }

        $sshKeyModels = [];

        if ($sshKeyIds) {
            $queryModel = $this->ormFactory->makeQueryModel();
            $queryModel->addWhere('guid', $sshKeyIds);

            foreach ($this->fetchSSHKey->fetch($queryModel) as $model) {
                $sshKeyModels[$model->getGuidAsBytes()] = $model;
            }
        }

        foreach ($this->fetchResults($params) as $record) {
            $model = new ServerModel();

            $model->setGuidAsBytes($record->guid);

            if ($record->project_guid) {
                $model->setProjectGuidAsBytes($record->project_guid);
            }

            $model->remoteId($record->remote_id);
            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->title($record->title);
            $model->slug($record->slug);
            $model->address($record->address);
            $model->sshPort((int) $record->ssh_port);
            $model->sshKeyModel($sshKeyModels[$record->ssh_key_guid] ?? null);
            $model->sshUserName($record->ssh_user_name);

            $adapter = $record->remote_service_adapter;

            if ($adapter && $this->di->has($adapter)) {
                $model->remoteServiceAdapter(
                    $this->di->get($record->remote_service_adapter)
                );
            }

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param QueryModelInterface $params
     * @return ServerRecord[]
     */
    private function fetchResults(QueryModelInterface $params): array
    {
        return $this->buildQuery->build(Server::class, $params)->fetchRecords();
    }
}
