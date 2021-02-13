<?php

declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use src\app\support\interfaces\StandardModelInterface;

interface PipelineModelInterface extends StandardModelInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function description(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function secretId(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function enableWebhook(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function webhookCheckForBranch(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function runBeforeEveryItem(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     *
     * @param PipelineItemModelInterface[]|null $val
     *
     * @return PipelineItemModelInterface[]
     */
    public function pipelineItems(?array $val = null) : array;

    /**
     * Adds a pipeline item
     *
     * @return mixed
     */
    public function addPipelineItem(PipelineItemModelInterface $model);
}
