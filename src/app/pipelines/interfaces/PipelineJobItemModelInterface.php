<?php

declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use DateTime;
use src\app\support\interfaces\HasGuidInterface;

interface PipelineJobItemModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function pipeline(
        ?PipelineModelInterface $val = null
    ) : ?PipelineModelInterface;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function pipelineJob(
        ?PipelineJobModelInterface $val = null
    ) : ?PipelineJobModelInterface;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function pipelineItem(
        ?PipelineItemModelInterface $val = null
    ) : ?PipelineItemModelInterface;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function hasFailed(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function logContent(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function finishedAt(?DateTime $val = null) : ?DateTime;
}
