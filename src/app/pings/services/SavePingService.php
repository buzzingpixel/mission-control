<?php
declare(strict_types=1);

namespace src\app\pings\services;

use Cocur\Slugify\Slugify;
use src\app\data\Ping\Ping;
use src\app\data\Ping\PingRecord;
use Ramsey\Uuid\UuidFactoryInterface;
use corbomite\db\Factory as OrmFactory;
use src\app\pings\events\PingBeforeSaveEvent;
use \src\app\pings\events\PingAfterSaveEvent;
use src\app\pings\interfaces\PingModelInterface;
use corbomite\db\interfaces\BuildQueryInterface;
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
            $beforeEvent = new PingBeforeSaveEvent($model, true);

            $this->eventDispatcher->dispatch(
                $beforeEvent->provider(),
                $beforeEvent->name(),
                $beforeEvent
            );

            $this->saveNew($model);

            $afterEvent = new PingAfterSaveEvent($model, true);

            $this->eventDispatcher->dispatch(
                $afterEvent->provider(),
                $afterEvent->name(),
                $afterEvent
            );

            return;
        }

        $beforeEvent = new PingBeforeSaveEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $this->finalSave($model, $existingRecord);

        $afterEvent = new PingAfterSaveEvent($model);

        $this->eventDispatcher->dispatch(
            $afterEvent->provider(),
            $afterEvent->name(),
            $afterEvent
        );
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
        $lastPingAt = $model->lastPingAt();

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

        if ($lastPingAt) {
            $record->last_ping_at = $lastPingAt->format('Y-m-d H:i:s');
            $record->last_ping_at_time_zone = $lastPingAt->getTimezone()->getName();
        }

        $record->added_at = $model->addedAt()->format('Y-m-d H:i:s');
        $record->added_at_time_zone = $model->addedAt()->getTimezone()->getName();

        $this->ormFactory->makeOrm()->persist($record);
    }
}
