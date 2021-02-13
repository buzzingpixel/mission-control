<?php

declare(strict_types=1);

namespace src\app\pipelines\models;

use corbomite\db\traits\UuidTrait;
use DateTime;
use src\app\pipelines\interfaces\PipelineItemModelInterface;
use src\app\pipelines\interfaces\PipelineJobItemModelInterface;
use src\app\pipelines\interfaces\PipelineJobModelInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use const PREG_SET_ORDER;
use function preg_match_all;
use function preg_replace;
use function str_replace;

class PipelineJobItemModel implements PipelineJobItemModelInterface
{
    use UuidTrait;

    /** @var PipelineModelInterface|null */
    private $pipeline;

    public function pipeline(
        ?PipelineModelInterface $val = null
    ) : ?PipelineModelInterface {
        return $this->pipeline = $val ?? $this->pipeline;
    }

    /** @var PipelineJobModelInterface|null */
    private $pipelineJob;

    public function pipelineJob(
        ?PipelineJobModelInterface $val = null
    ) : ?PipelineJobModelInterface {
        return $this->pipelineJob = $val ?? $this->pipelineJob;
    }

    /** @var PipelineItemModelInterface|null */
    private $pipelineItem;

    public function pipelineItem(
        ?PipelineItemModelInterface $val = null
    ) : ?PipelineItemModelInterface {
        return $this->pipelineItem = $val ?? $this->pipelineItem;
    }

    /** @var bool */
    private $hasFailed = false;

    public function hasFailed(?bool $val = null) : bool
    {
        return $this->hasFailed = $val ?? $this->hasFailed;
    }

    /** @var string */
    private $logContent = '';

    public function logContent(?string $val = null) : string
    {
        return $this->logContent = $val ?? $this->logContent;
    }

    /** @var ?DateTime */
    private $finishedAt;

    public function finishedAt(?DateTime $val = null) : ?DateTime
    {
        return $this->finishedAt = $val ?? $this->finishedAt;
    }

    public function getPreparedScriptForExecution() : string
    {
        $dateTime = $this->pipelineJob->jobAddedAt();

        // Do {{timestamp}} replacement
        $preparedString = str_replace(
            '{{timestamp}}',
            $dateTime->getTimestamp(),
            $this->pipelineItem->getFullScriptForExecution()
        );

        // Find instances of {{time "FORMAT_HERE"}} or {{time 'FORMAT_HERE'}}
        preg_match_all(
            '/{{time (?:"|\')(.+?)(?:"|\')}}/',
            $preparedString,
            $timeMatches,
            PREG_SET_ORDER
        );

        // Do replacements
        foreach ($timeMatches as $match) {
            $replacement = $dateTime->format($match[1]);

            $preparedString = preg_replace(
                '/' . $match[0] . '/',
                $replacement,
                $preparedString,
                1
            );
        }

        return trim($preparedString);
    }
}
