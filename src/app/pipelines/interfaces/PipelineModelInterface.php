<?php
declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use src\app\support\interfaces\StandardModelInterface;

interface PipelineModelInterface extends StandardModelInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function description(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function secretId(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param PipelineItemModelInterface[]|null $val
     * @return PipelineItemModelInterface[]
     */
    public function pipelineItems(?array $val = null): array;

    /**
     * Adds a pipeline item
     * @param PipelineItemModelInterface $model
     */
    public function addPipelineItem(PipelineItemModelInterface $model);
}
