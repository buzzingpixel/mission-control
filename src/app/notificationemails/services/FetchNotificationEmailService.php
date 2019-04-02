<?php

declare(strict_types=1);

namespace src\app\notificationemails\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\data\NotificationEmail\NotificationEmail;
use src\app\data\NotificationEmail\NotificationEmailRecord;
use src\app\notificationemails\interfaces\NotificationEmailModelInterface;
use src\app\notificationemails\models\NotificationEmailModel;

class FetchNotificationEmailService
{
    /** @var BuildQueryInterface */
    private $buildQuery;

    public function __construct(BuildQueryInterface $buildQuery)
    {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return NotificationEmailModelInterface[]
     */
    public function __invoke(QueryModelInterface $params) : array
    {
        return $this->fetch($params);
    }

    /**
     * @return NotificationEmailModelInterface[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new NotificationEmailModel();

            $model->setGuidAsBytes($record->guid);
            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->emailAddress($record->email_address);

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return NotificationEmailRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(NotificationEmail::class, $params)->fetchRecords();
    }
}
