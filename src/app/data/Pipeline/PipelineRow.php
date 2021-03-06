<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\Pipeline;

use Atlas\Table\Row;

/**
 * @property mixed $guid binary(16) NOT NULL
 * @property mixed $project_guid binary(16)
 * @property mixed $is_active tinyint(3,0) NOT NULL
 * @property mixed $enable_webhook tinyint(3,0) NOT NULL
 * @property mixed $webhook_check_for_branch varchar(255)
 * @property mixed $title varchar(255) NOT NULL
 * @property mixed $slug varchar(255) NOT NULL
 * @property mixed $description varchar(255) NOT NULL
 * @property mixed $secret_id varchar(255) NOT NULL
 * @property mixed $run_before_every_item text(65535) NOT NULL
 */
class PipelineRow extends Row
{
    protected $cols = [
        'guid' => null,
        'project_guid' => 'NULL',
        'is_active' => 1,
        'enable_webhook' => 0,
        'webhook_check_for_branch' => '\'\'',
        'title' => null,
        'slug' => null,
        'description' => null,
        'secret_id' => null,
        'run_before_every_item' => null,
    ];
}
