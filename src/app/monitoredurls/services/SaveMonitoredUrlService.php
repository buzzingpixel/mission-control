<?php
declare(strict_types=1);

namespace src\app\monitoredurls\services;

use Cocur\Slugify\Slugify;
use corbomite\events\EventDispatcher;
use corbomite\db\Factory as DbFactory;
use corbomite\db\Factory as OrmFactory;
use src\app\data\MonitoredUrl\MonitoredUrl;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterSaveEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeSaveEvent;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlModelException;
use src\app\monitoredurls\exceptions\MonitoredUrlNameNotUniqueException;

class SaveMonitoredUrlService
{
    private $slugify;
    private $ormFactory;
    private $buildQuery;
    private $eventDispatcher;
    private $dbFactory;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher,
        DbFactory $dbFactory
    ) {
        $this->slugify = $slugify;
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
        $this->dbFactory = $dbFactory;
    }

    /**
     * @throws InvalidMonitoredUrlModelException
     * @throws MonitoredUrlNameNotUniqueException
     */
    public function __invoke(MonitoredUrlModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidMonitoredUrlModelException
     * @throws MonitoredUrlNameNotUniqueException
     */
    public function save(MonitoredUrlModelInterface $model): void
    {
        if (! $model->title() || ! $model->url()) {
            throw new InvalidMonitoredUrlModelException();
        }

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->dbFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(MonitoredUrl::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new MonitoredUrlNameNotUniqueException();
        }

        $fetchModel = $this->dbFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(MonitoredUrl::class, $fetchModel)->fetchRecord();

        if (! $existingRecord) {
            $beforeEvent = new MonitoredUrlBeforeSaveEvent($model, true);

            $this->eventDispatcher->dispatch(
                $beforeEvent->provider(),
                $beforeEvent->name(),
                $beforeEvent
            );

            $this->saveNewProject($model);

            $afterEvent = new MonitoredUrlAfterSaveEvent($model, true);

            $this->eventDispatcher->dispatch(
                $afterEvent->provider(),
                $afterEvent->name(),
                $afterEvent
            );

            return;
        }

        $beforeEvent = new MonitoredUrlBeforeSaveEvent($model);

        $this->eventDispatcher->dispatch(
            $beforeEvent->provider(),
            $beforeEvent->name(),
            $beforeEvent
        );

        $this->finalSave($model, $existingRecord);

        $afterEvent = new MonitoredUrlAfterSaveEvent($model);

        $this->eventDispatcher->dispatch(
            $afterEvent->provider(),
            $afterEvent->name(),
            $afterEvent
        );
    }

    private function saveNewProject(MonitoredUrlModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(MonitoredUrl::class);

        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        MonitoredUrlModelInterface $model,
        MonitoredUrlRecord $record
    ): void {
        $record->project_guid = $model->getProjectGuidAsBytes();
        $record->is_active = $model->isActive();
        $record->title = $model->title();
        $record->slug = $model->slug();
        $record->url = $model->url();
        $record->pending_error = $model->pendingError();
        $record->has_error = $model->hasError();
        $record->checked_at = $model->checkedAt()->format('Y-m-d H:i:s');
        $record->checked_at_time_zone = $model->checkedAt()->getTimezone()->getName();
        $record->added_at = $model->addedAt()->format('Y-m-d H:i:s');
        $record->added_at_time_zone = $model->addedAt()->getTimezone()->getName();

        $this->ormFactory->makeOrm()->persist($record);
    }
}
