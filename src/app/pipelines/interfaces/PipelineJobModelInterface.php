<?php

declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use DateTime;
use src\app\support\interfaces\HasGuidInterface;

interface PipelineJobModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function pipeline(
        ?PipelineModelInterface $val = null
    ) : ?PipelineModelInterface;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function hasStarted(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function isFinished(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function hasFailed(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function percentComplete(?float $val = null) : float;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     */
    public function jobAddedAt(?DateTime $val = null) : DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set.
     */
    public function jobFinishedAt(?DateTime $val = null) : ?DateTime;

    /**
     * Returns the value. Sets value if incoming argument is set
     *
     * @param PipelineJobItemModelInterface[]|null $val
     *
     * @return PipelineJobItemModelInterface[]
     */
    public function pipelineJobItems(?array $val = null) : array;

    /**
     * Adds a pipeline job item
     *
     * @param PipelineItemModelInterface $model
     *
     * @return mixed
     */
    public function addPipelineJobItem(PipelineJobItemModelInterface $model);
}
