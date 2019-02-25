<?php
declare(strict_types=1);

namespace src\app\servers\services;

use Cocur\Slugify\Slugify;
use src\app\data\Server\Server;
use src\app\data\Server\ServerRecord;
use corbomite\db\Factory as OrmFactory;
use src\app\servers\events\ServerAfterSaveEvent;
use corbomite\db\interfaces\BuildQueryInterface;
use Atlas\Table\Exception as AtlasTableException;
use src\app\servers\events\ServerBeforeSaveEvent;
use src\app\servers\interfaces\ServerModelInterface;
use src\app\servers\exceptions\TitleNotUniqueException;
use corbomite\events\interfaces\EventDispatcherInterface;
use src\app\servers\exceptions\InvalidServerModelException;

class SaveServerService
{
    private $slugify;
    private $ormFactory;
    private $buildQuery;
    private $eventDispatcher;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidServerModelException
     * @throws TitleNotUniqueException
     */
    public function __invoke(ServerModelInterface $model): void
    {
        $this->save($model);
    }

    /**
     * @throws InvalidServerModelException
     * @throws TitleNotUniqueException
     */
    public function save(ServerModelInterface $model): void
    {
        if (! $model->title() ||
            ! $model->address() ||
            ! $model->sshPort() ||
            ! $model->sshKeyModel() ||
            ! $model->sshUserName()
        ) {
            throw new InvalidServerModelException();
        }

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(Server::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new TitleNotUniqueException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(Server::class, $fetchModel)->fetchRecord();

        $isNew = ! $existingRecord;

        $this->eventDispatcher->dispatch(new ServerBeforeSaveEvent($model, $isNew));

        $isNew ? $this->saveNew($model) : $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new ServerAfterSaveEvent($model, $isNew));
    }

    private function saveNew(ServerModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Server::class);

        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function finalSave(ServerModelInterface $model, ServerRecord $record): void
    {
        $record->project_guid = $model->getProjectGuidAsBytes();
        $record->is_active = $model->isActive();
        $record->title = $model->title();
        $record->slug = $model->slug();
        $record->remote_service_adapter = null;
        $record->remote_id = $model->remoteId();
        $record->address = $model->address();
        $record->ssh_port = $model->sshPort();
        $record->ssh_user_name = $model->sshUserName();
        $record->ssh_key_guid = null;

        if ($sshKeyModel = $model->sshKeyModel()) {
            $record->ssh_key_guid = $sshKeyModel->getGuidAsBytes();
        }

        $remoteServiceAdapter = $model->remoteServiceAdapter();

        if ($remoteServiceAdapter) {
            $record->remote_service_adapter = get_class($remoteServiceAdapter);
        }

        try {
            $this->ormFactory->makeOrm()->persist($record);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() === 'Expected 1 row affected, actual 0.') {
                return;
            }

            throw $e;
        }
    }
}
