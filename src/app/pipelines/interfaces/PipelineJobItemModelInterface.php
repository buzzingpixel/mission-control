<?php
declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use DateTime;
use src\app\support\interfaces\HasGuidInterface;

interface PipelineJobItemModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param PipelineModelInterface|null $val
     * @return PipelineModelInterface|null
     */
    public function pipeline(
        ?PipelineModelInterface $val = null
    ): ?PipelineModelInterface;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param PipelineJobModelInterface|null $val
     * @return PipelineJobModelInterface|null
     */
    public function pipelineJob(
        ?PipelineJobModelInterface $val = null
    ): ?PipelineJobModelInterface;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param PipelineItemModelInterface|null $val
     * @return PipelineItemModelInterface|null
     */
    public function pipelineItem(
        ?PipelineItemModelInterface $val = null
    ): ?PipelineItemModelInterface;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function hasFailed(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function logContent(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param DateTime|null $val
     * @return DateTime|null
     */
    public function finishedAt(?DateTime $val = null): ?DateTime;
}
