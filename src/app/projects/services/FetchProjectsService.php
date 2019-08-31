<?php

declare(strict_types=1);

namespace src\app\projects\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use DateTime;
use DateTimeZone;
use src\app\data\Project\Project;
use src\app\data\Project\ProjectRecord;
use src\app\projects\interfaces\ProjectModelInterface;
use src\app\projects\models\ProjectModel;
use function is_array;
use function is_string;
use function json_decode;

class FetchProjectsService
{
    /** @var BuildQueryInterface */
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return ProjectModelInterface[]
     */
    public function __invoke(QueryModelInterface $params) : array
    {
        return $this->fetch($params);
    }

    /**
     * @return ProjectModelInterface[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new ProjectModel();

            $keyValueItems = $record->key_value_items;

            if (! is_string($keyValueItems)) {
                $keyValueItems = '';
            }

            $keyValueItems = json_decode($keyValueItems, true);

            if (! is_array($keyValueItems)) {
                $keyValueItems = [];
            }

            $model->setGuidAsBytes($record->guid);
            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->title($record->title);
            $model->slug($record->slug);
            $model->description($record->description);
            $model->keyValueItems($keyValueItems);
            /** @noinspection PhpUnhandledExceptionInspection */
            $model->addedAt(new DateTime(
                $record->added_at,
                new DateTimeZone($record->added_at_time_zone)
            ));

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return ProjectRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(Project::class, $params)->fetchRecords();
    }
}
