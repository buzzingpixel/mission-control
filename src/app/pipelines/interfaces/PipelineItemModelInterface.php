<?php
declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use src\app\support\interfaces\HasGuidInterface;

interface PipelineItemModelInterface extends HasGuidInterface
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
     * @param string|null $val
     * @return string
     */
    public function description(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function script(?string $val = null): string;
}
