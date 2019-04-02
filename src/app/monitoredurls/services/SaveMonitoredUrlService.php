<?php

declare(strict_types=1);

namespace src\app\monitoredurls\services;

use Atlas\Table\Exception as AtlasTableException;
use Cocur\Slugify\Slugify;
use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\events\EventDispatcher;
use DateTimeZone;
use src\app\data\MonitoredUrl\MonitoredUrl;
use src\app\data\MonitoredUrl\MonitoredUrlRecord;
use src\app\monitoredurls\events\MonitoredUrlAfterSaveEvent;
use src\app\monitoredurls\events\MonitoredUrlBeforeSaveEvent;
use src\app\monitoredurls\exceptions\InvalidMonitoredUrlModelException;
use src\app\monitoredurls\exceptions\MonitoredUrlNameNotUniqueException;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class SaveMonitoredUrlService
{
    /** @var Slugify */
    private $slugify;
    /** @var OrmFactory */
    private $ormFactory;
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(
        Slugify $slugify,
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery,
        EventDispatcher $eventDispatcher
    ) {
        $this->slugify         = $slugify;
        $this->ormFactory      = $ormFactory;
        $this->buildQuery      = $buildQuery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws InvalidMonitoredUrlModelException
     * @throws MonitoredUrlNameNotUniqueException
     */
    public function __invoke(MonitoredUrlModelInterface $model) : void
    {
        $this->save($model);
    }

    /**
     * @throws InvalidMonitoredUrlModelException
     * @throws MonitoredUrlNameNotUniqueException
     */
    public function save(MonitoredUrlModelInterface $model) : void
    {
        if (! $model->title() || ! $model->url()) {
            throw new InvalidMonitoredUrlModelException();
        }

        $model->slug($this->slugify->slugify($model->title()));

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhereGroup(false);
        $fetchModel->addWhere('title', $model->title());
        $fetchModel->addWhere('slug', $model->slug(), '=', true);
        $existing = $this->buildQuery->build(MonitoredUrl::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new MonitoredUrlNameNotUniqueException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(MonitoredUrl::class, $fetchModel)->fetchRecord();

        if (! $existingRecord) {
            $this->eventDispatcher->dispatch(new MonitoredUrlBeforeSaveEvent($model, true));

            $this->saveNewProject($model);

            $this->eventDispatcher->dispatch(new MonitoredUrlAfterSaveEvent($model, true));

            return;
        }

        $this->eventDispatcher->dispatch(new MonitoredUrlBeforeSaveEvent($model));

        $this->finalSave($model, $existingRecord);

        $this->eventDispatcher->dispatch(new MonitoredUrlAfterSaveEvent($model));
    }

    private function saveNewProject(MonitoredUrlModelInterface $model) : void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(MonitoredUrl::class);

        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        MonitoredUrlModelInterface $model,
        MonitoredUrlRecord $record
    ) : void {
        $checkedAt = $model->checkedAt();
        $addedAt   = $model->addedAt();

        $checkedAt->setTimezone(new DateTimeZone('UTC'));
        $addedAt->setTimezone(new DateTimeZone('UTC'));

        $record->project_guid         = $model->getProjectGuidAsBytes();
        $record->is_active            = $model->isActive();
        $record->title                = $model->title();
        $record->slug                 = $model->slug();
        $record->url                  = $model->url();
        $record->pending_error        = $model->pendingError();
        $record->has_error            = $model->hasError();
        $record->checked_at           = $checkedAt->format('Y-m-d H:i:s');
        $record->checked_at_time_zone = $checkedAt->getTimezone()->getName();
        $record->added_at             = $addedAt->format('Y-m-d H:i:s');
        $record->added_at_time_zone   = $addedAt->getTimezone()->getName();

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
