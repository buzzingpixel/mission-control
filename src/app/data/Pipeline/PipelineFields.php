<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\Pipeline;

/**
 * @property mixed $guid binary(16) NOT NULL
 * @property mixed $project_guid binary(16)
 * @property mixed $is_active tinyint(3,0) NOT NULL
 * @property mixed $title varchar(255) NOT NULL
 * @property mixed $slug varchar(255) NOT NULL
 * @property mixed $description varchar(255) NOT NULL
 * @property mixed $secret_id varchar(255) NOT NULL
 * @property null|\src\app\data\PipelineItem\PipelineItemRecordSet $pipeline_items
 */
trait PipelineFields
{
}