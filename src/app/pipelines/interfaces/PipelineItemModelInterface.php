<?php
declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use corbomite\db\interfaces\UuidModelInterface;
use src\app\support\interfaces\HasGuidInterface;

interface PipelineItemModelInterface extends HasGuidInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $guid
     * @return string
     */
    public function pipelineGuid(?string $val = null): string;

    /**
     * Gets the UuidModel for the pipeline guid
     * @return UuidModelInterface
     */
    public function pipelineGuidAsModel(): UuidModelInterface;

    /**
     * Gets the Pipeline GUID as bytes for saving to the database in binary
     * @return string
     */
    public function getPipelineGuidAsBytes(): string;

    /**
     * Sets the Pipeline GUID from bytes coming from the database binary column
     * @param string $bytes
     */
    public function setPipelineGuidAsBytes(string $bytes);

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function script(?string $val = null): string;
}
