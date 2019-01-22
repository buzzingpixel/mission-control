<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace src\app\data\ActionQueueBatch;

/**
 * @property mixed $guid varchar(255) NOT NULL
 * @property mixed $name varchar(255) NOT NULL
 * @property mixed $title varchar(255) NOT NULL
 * @property mixed $has_started tinyint(3,0) NOT NULL
 * @property mixed $is_finished tinyint(3,0) NOT NULL
 * @property mixed $finished_due_to_error tinyint(3,0) NOT NULL
 * @property mixed $percent_complete float(12) NOT NULL
 * @property mixed $added_at datetime NOT NULL
 * @property mixed $added_at_time_zone varchar(255) NOT NULL
 * @property mixed $finished_at datetime
 * @property mixed $finished_at_time_zone varchar(255)
 * @property mixed $context text(65535)
 */
trait ActionQueueBatchFields
{
}
