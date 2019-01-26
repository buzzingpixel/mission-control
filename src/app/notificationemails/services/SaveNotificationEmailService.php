<?php
declare(strict_types=1);

namespace src\app\notificationemails\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use Atlas\Table\Exception as AtlasTableException;
use src\app\data\NotificationEmail\NotificationEmail;
use src\app\data\NotificationEmail\NotificationEmailRecord;
use src\app\notificationemails\interfaces\NotificationEmailModelInterface;
use \src\app\notificationemails\exceptions\NotificationEmailNotUniqueException;
use src\app\notificationemails\exceptions\InvalidNotificationEmailModelException;

class SaveNotificationEmailService
{
    private $ormFactory;
    private $buildQuery;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery
    ) {
        $this->ormFactory = $ormFactory;
        $this->buildQuery = $buildQuery;
    }

    /**
     * @throws InvalidNotificationEmailModelException
     * @throws NotificationEmailNotUniqueException
     */
    public function __invoke(NotificationEmailModelInterface $model)
    {
        $this->save($model);
    }

    /**
     * @throws InvalidNotificationEmailModelException
     * @throws NotificationEmailNotUniqueException
     */
    public function save(NotificationEmailModelInterface $model): void
    {
        if (! $model->emailAddress()) {
            throw new InvalidNotificationEmailModelException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes(), '!=');
        $fetchModel->addWhere('email_address', $model->emailAddress());
        $existing = $this->buildQuery->build(NotificationEmail::class, $fetchModel)->fetchRecord();

        if ($existing) {
            throw new NotificationEmailNotUniqueException();
        }

        $fetchModel = $this->ormFactory->makeQueryModel();
        $fetchModel->limit(1);
        $fetchModel->addWhere('guid', $model->getGuidAsBytes());
        $existingRecord = $this->buildQuery->build(NotificationEmail::class, $fetchModel)->fetchRecord();

        if (! $existingRecord) {
            $this->saveNew($model);
            return;
        }
        $this->finalSave($model, $existingRecord);
    }

    private function saveNew(NotificationEmailModelInterface $model): void
    {
        $orm = $this->ormFactory->makeOrm();

        $record = $orm->newRecord(NotificationEmail::class);

        $record->guid = $model->getGuidAsBytes();

        $this->finalSave($model, $record);
    }

    private function finalSave(
        NotificationEmailModelInterface $model,
        NotificationEmailRecord $record
    ): void {
        $record->is_active = $model->isActive();
        $record->email_address = $model->emailAddress();

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
