<?php

declare(strict_types=1);

namespace src\app\servers\services;

use corbomite\db\interfaces\BuildQueryInterface;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\data\SshKey\SshKey;
use src\app\data\SshKey\SshKeyRecord;
use src\app\servers\interfaces\SSHKeyModelInterface;
use src\app\servers\models\SSHKeyModel;

class FetchSSHKeyService
{
    /** @var BuildQueryInterface */
    private $buildQuery;

    public function __construct(
        BuildQueryInterface $buildQuery
    ) {
        $this->buildQuery = $buildQuery;
    }

    /**
     * @return SSHKeyModelInterface[]
     */
    public function __invoke(QueryModelInterface $params) : array
    {
        return $this->fetch($params);
    }

    /**
     * @return SSHKeyModelInterface[]
     */
    public function fetch(QueryModelInterface $params) : array
    {
        $models = [];

        foreach ($this->fetchResults($params) as $record) {
            $model = new SSHKeyModel();

            $model->setGuidAsBytes($record->guid);
            $model->isActive($record->is_active === 1 || $record->is_active === '1');
            $model->title($record->title);
            $model->slug($record->slug);
            $model->public($record->public);
            $model->private($record->private);

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @return SshKeyRecord[]
     */
    private function fetchResults(QueryModelInterface $params) : array
    {
        return $this->buildQuery->build(SshKey::class, $params)->fetchRecords();
    }
}
