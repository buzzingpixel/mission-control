<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\PipelineJobItem;

/**
 * @property mixed $guid binary(16) NOT NULL
 * @property mixed $pipeline_guid binary(16)
 * @property mixed $pipeline_job_guid binary(16)
 * @property mixed $pipeline_item_guid binary(16)
 * @property mixed $order int(10,0) NOT NULL
 * @property mixed $has_failed tinyint(3,0) NOT NULL
 * @property mixed $log_content longtext(4294967295) NOT NULL
 * @property mixed $finished_at datetime
 * @property mixed $finished_at_time_zone varchar(255)
 * @property null|false|\src\app\data\Pipeline\PipelineRecord $pipeline
 * @property null|false|\src\app\data\PipelineJob\PipelineJobRecord $pipeline_job
 * @property null|false|\src\app\data\PipelineItem\PipelineItemRecord $pipeline_item
 */
trait PipelineJobItemFields
{
}
