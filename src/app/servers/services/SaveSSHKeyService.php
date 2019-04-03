<?php

declare(strict_types=1);

namespace src\app\servers\services;

use Atlas\Table\Exception as AtlasTableException;
use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\interfaces\EventDispatcherInterface;
use src\app\data\SshKey\SshKey;
use src\app\data\SshKey\SshKeyRecord;
use src\app\servers\events\SSHKeyAfterSaveEvent;
use src\app\servers\events\SSHKeyBeforeSaveEvent;
use src\app\servers\exceptions\InvalidSSHKeyModelException;
use src\app\servers\exceptions\TitleNotUniqueException;
use src\app\servers\interfaces\SSHKeyModelInterface;

class SaveSSHKeyService
{
    /** @var Slugify */
    private $slugify;
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->slugify         = $slugify;
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidSSHKeyModelException
     * @throws TitleNotUniqueException
     */
    public function __invoke(SSHKeyModelInterface $model) : void
    {
        $this->save($model);
    }

    /**
     * @throws InvalidSSHKeyModelException
     * @throws TitleNotUniqueException
     */
    public function save(SSHKeyModelInterface $model) : void
    {
        if (! $model->title() ||
            ! $model->public() ||
            ! $model->private()
        ) {
            throw new InvalidSSHKeyModelException();
        }

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(SshKey::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new TitleNotUniqueException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(SshKey::class, $fetchModel)->fetchRecord();

        $isNew = ! $existingRecord;

        $this->eventDispatcher->dispatch(new SSHKeyBeforeSaveEvent($model, $isNew));

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpParamsInspection */
        $isNew ? $this->saveNew($model) : $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new SSHKeyAfterSaveEvent($model, $isNew));
    }

    private function saveNew(SSHKeyModelInterface $model) : void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(SshKey::class);

        $record->guid = $model->getGuidAsBytes();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->finalSave($model, $record);
    }

    private function finalSave(SSHKeyModelInterface $model, SshKeyRecord $record) : void
    {
        $record->is_active = $model->isActive();
        $record->title     = $model->title();
        $record->slug      = $model->slug();
        $record->public    = $model->public();
        $record->private   = $model->private();

        try {
            $this->ormFactory->makeOrm()->persist($record);
        } catch (AtlasTableException $e) {
            if ($e->getMessage() === 'Expected 1 row affected, actual 0.') {
                return;
            }

            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
    }
}
