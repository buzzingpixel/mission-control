<?php

declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use src\app\servers\interfaces\ServerModelInterface;
use src\app\support\interfaces\HasGuidInterface;

interface PipelineItemModelInterface extends HasGuidInterface
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
    public function description(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function script(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set
     *
     * @param ServerModelInterface[]|null $val
     *
     * @return ServerModelInterface[]
     */
    public function servers(?array $val = null) : array;

    /**
     * Adds a server
     *
     * @return mixed
     */
    public function addServer(ServerModelInterface $server);
}
