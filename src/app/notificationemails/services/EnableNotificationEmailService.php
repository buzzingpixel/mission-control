<?php

declare(strict_types=1);

namespace src\app\notificationemails\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\interfaces\BuildQueryInterface;
use src\app\data\NotificationEmail\NotificationEmail;
use src\app\data\NotificationEmail\NotificationEmailRecord;
use src\app\notificationemails\interfaces\NotificationEmailModelInterface;

class EnableNotificationEmailService
{
    /** @var BuildQueryInterface */
    private $buildQuery;
    /** @var OrmFactory */
    private $ormFactory;

    public function __construct(
        OrmFactory $ormFactory,
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
        $this->ormFactory = $ormFactory;
    }

    public function __invoke(NotificationEmailModelInterface $model) : void
    {
        $this->enable($model);
    }

    public function enable(NotificationEmailModelInterface $model) : void
    {
        $record = $this->fetchRecord($model);

        $record->is_active = 1;

        $this->ormFactory->makeOrm()->persist($record);
    }

    private function fetchRecord(NotificationEmailModelInterface $model) : NotificationEmailRecord
    {
        $params = $this->ormFactory->makeQueryModel();
        $params->addWhere('guid', $model->getGuidAsBytes());

        return $this->buildQuery->build(NotificationEmail::class, $params)->fetchRecord();
    }
}
