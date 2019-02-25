<?php
declare(strict_types=1);

namespace src\app\pings\services;

use DateTimeZone;
use Cocur\Slugify\Slugify;
use src\app\data\Ping\Ping;
use src\app\data\Ping\PingRecord;
use Ramsey\Uuid\UuidFactoryInterface;
use corbomite\db\Factory as OrmFactory;
use src\app\pings\events\PingAfterSaveEvent;
use src\app\pings\events\PingBeforeSaveEvent;
use src\app\pings\interfaces\PingModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
use Atlas\Table\Exception as AtlasTableException;
use src\app\pings\exceptions\InvalidPingModelException;
use src\app\pings\exceptions\PingNameNotUniqueException;
use corbomite\events\interfaces\EventDispatcherInterface;

class SavePingService
{
    private $slugify;
    private $ormFactory;
    private $buildQuery;
    private $uuidFactory;
    private $eventDispatcher;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        UuidFactoryInterface $uuidFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->uuidFactory = $uuidFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidPingModelException
     * @throws PingNameNotUniqueException
     */
    public function __invoke(PingModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidPingModelException
     * @throws PingNameNotUniqueException
     */
    public function save(PingModelInterface $model): void
    {
        if (! $model->title() || ! $model->expectEvery() || ! $model->warnAfter()) {
            throw new InvalidPingModelException();
        }

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(Ping::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new PingNameNotUniqueException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(Ping::class, $fetchModel)->fetchRecord();

        if (! $existingRecord) {
            $this->eventDispatcher->dispatch(new PingBeforeSaveEvent($model, true));

            $this->saveNew($model);

            $this->eventDispatcher->dispatch(new PingAfterSaveEvent($model, true));

            return;
        }

        $this->eventDispatcher->dispatch(new PingBeforeSaveEvent($model));

        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new PingAfterSaveEvent($model));
    }

    private function saveNew(PingModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(Ping::class);

        $record->guid = $model->getGuidAsBytes();

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->ping_id = $this->uuidFactory->uuid4()->toString();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        PingModelInterface $model,
        PingRecord $record
    ): void {
        $record->project_guid = $model->getProjectGuidAsBytes();
        $record->is_active = $model->isActive();
        $record->title = $model->title();
        $record->slug = $model->slug();
        $record->pending_error = $model->pendingError();
        $record->has_error = $model->hasError();
        $record->expect_every = $model->expectEvery();
        $record->warn_after = $model->warnAfter();
        $record->last_ping_at = null;
        $record->last_ping_at_time_zone = null;

        if ($lastPingAt = $model->lastPingAt()) {
            $lastPingAt->setTimezone(new DateTimeZone('UTC'));
            $record->last_ping_at = $lastPingAt->format('Y-m-d H:i:s');
            $record->last_ping_at_time_zone = $lastPingAt->getTimezone()->getName();
        }

        $record->last_notification_at = null;
        $record->last_notification_at_time_zone = null;

        if ($lastNotificationAt = $model->lastNotificationAt()) {
            $lastNotificationAt->setTimezone(new DateTimeZone('UTC'));
            $record->last_notification_at = $lastNotificationAt->format('Y-m-d H:i:s');
            $record->last_notification_at_time_zone = $lastNotificationAt->getTimezone()->getName();
        }

        $addedAt = $model->addedAt();
        $addedAt->setTimezone(new DateTimeZone('UTC'));

        $record->added_at = $addedAt->format('Y-m-d H:i:s');
        $record->added_at_time_zone = $addedAt->getTimezone()->getName();

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
