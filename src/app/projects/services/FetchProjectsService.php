<?php
declare(strict_types=1);

namespace src\app\projects\services;

use DateTime;
use DateTimeZone;
use src\app\data\Project\Project;
use src\app\data\Project\ProjectRecord;
use src\app\projects\models\ProjectModel;
use src\app\datasupport\BuildQueryInterface;
use src\app\datasupport\FetchDataParamsInterface;
use src\app\projects\interfaces\ProjectModelInterface;

class FetchProjectsService
{
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return ProjectModelInterface[]
     */
    public function __invoke(FetchDataParamsInterface $params): array
    {
        return $this->fetch($params);
    }

    /**
     * @return ProjectModelInterface[]
     */
    public function fetch(FetchDataParamsInterface $params): array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new ProjectModel();

            $model->guid($record->guid);
            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->title($record->title);
            $model->slug($record->slug);
            $model->description($record->description);
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
     * @param $params
     * @return ProjectRecord[]
     */
    private function fetchResults($params): array
    {
        return $this->buildQuery->build(Project::class, $params)->fetchRecords();
    }
}
