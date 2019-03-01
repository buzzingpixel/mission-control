<?php
declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use DateTime;
use src\app\support\interfaces\HasGuidInterface;

interface PipelineJobModelInterface extends HasGuidInterface
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
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function hasStarted(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function isFinished(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param bool|null $val
     * @return bool
     */
    public function hasFailed(?bool $val = null): bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param float|null $val
     * @return float
     */
    public function percentComplete(?float $val = null): float;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * @param DateTime|null $val
     * @return DateTime
     */
    public function jobAddedAt(?DateTime $val = null): DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * @param DateTime|null $val
     * @return DateTime|null
     */
    public function jobFinishedAt(?DateTime $val = null): ?DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param PipelineJobItemModelInterface[]|null $val
     * @return PipelineJobItemModelInterface[]
     */
    public function pipelineJobItems(?array $val = null): array;

    /**
     * Adds a pipeline job item
     * @param PipelineItemModelInterface $model
     */
    public function addPipelineJobItem(PipelineJobItemModelInterface $model);
}
