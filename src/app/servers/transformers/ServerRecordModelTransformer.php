<?php

declare(strict_types=1);

namespace src\app\servers\transformers;

use Atlas\Mapper\Record;
use corbomite\db\Factory as OrmFactory;
use Psr\Container\ContainerInterface;
use src\app\data\Server\ServerRecord;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\models\ServerModel;
use src\app\servers\models\SSHKeyModel;
use src\app\servers\services\FetchSSHKeyService;
use Traversable;
use function array_map;
use function is_array;
use function iterator_to_array;

class ServerRecordModelTransformer
{
    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @param Traversable|iterable|array|Record $recordSet
     *
     * @return SSHKeyModel[]
     */
    public function transformRecordSet($recordSet) : array
    {
        if ($recordSet === null) {
            return [];
        }

        $recordArray = is_array($recordSet) ?
            $recordSet :
            iterator_to_array($recordSet);

        $sshKeyIds = array_map(static function (ServerRecord $record) {
            return $record->ssh_key_guid;
        }, $recordArray);

        $sshKeyModels = $this->getSSHKeyModels($sshKeyIds);

        return array_map(
            function (ServerRecord $record) use ($sshKeyModels) {
                return $this->transformRecord($record, $sshKeyModels);
            },
            $recordArray
        );
    }

    /**
     * @param SSHKeyModel|null $sshKeyModels
     */
    public function transformRecord(
        ServerRecord $record,
        ?array $sshKeyModels = null
    ) : ServerModelInterface {
        if ($sshKeyModels === null) {
            $sshKeyModels = $this->getSSHKeyModels([$record->ssh_key_guid]);
        }

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

        return $model;
    }

    /**
     * @param mixed[] $sshKeyIds
     *
     * @return SSHKeyModel[]
     */
    private function getSSHKeyModels(array $sshKeyIds) : array
    {
        if (! $sshKeyIds) {
            return [];
        }

        $fetchSSHKeys = $this->di->get(FetchSSHKeyService::class);

        $ormFactory = $this->di->get(OrmFactory::class);

        $queryModel = $ormFactory->makeQueryModel();

        $queryModel->addWhere('guid', $sshKeyIds);

        $sshKeyModels = [];

        array_map(
            static function (SSHKeyModel $model) use (&$sshKeyModels) : void {
                $sshKeyModels[$model->getGuidAsBytes()] = $model;
            },
            $fetchSSHKeys->fetch($queryModel)
        );

        return $sshKeyModels;
    }
}
