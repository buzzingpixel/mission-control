<?php

declare(strict_types=1);

namespace src\app\pipelines\models;

use corbomite\db\traits\UuidTrait;
use src\app\pipelines\interfaces\PipelineItemModelInterface;
use src\app\pipelines\interfaces\PipelineModelInterface;
use src\app\servers\interfaces\ServerModelInterface;

class PipelineItemModel implements PipelineItemModelInterface
{
    use UuidTrait;

    /** @var ?PipelineModelInterface */
    private $pipeline;

    public function pipeline(
        ?PipelineModelInterface $val = null
    ) : ?PipelineModelInterface {
        return $this->pipeline = $val ?? $this->pipeline;
    }

    /** @var string */
    private $description = '';

    public function description(?string $val = null) : string
    {
        return $this->description = $val ?? $this->description;
    }

    /** @var string */
    private $script = '';

    public function script(?string $val = null) : string
    {
        return $this->script = $val ?? $this->script;
    }

    /** @var ServerModelInterface[] */
    private $servers = [];

    /**
     * @param ServerModelInterface[] $val
     *
     * @return ServerModelInterface[]
     */
    public function servers(?array $val = null) : array
    {
        if ($val === null) {
            return $this->servers;
        }

        $this->servers = [];

        foreach ($val as $model) {
            $this->addServer($model);
        }

        return $this->servers;
    }

    public function addServer(ServerModelInterface $model) : void
    {
        $this->servers[] = $model;
    }
}
