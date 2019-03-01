<?php
declare(strict_types=1);

namespace src\app\pipelines\interfaces;

use DateTime;
use corbomite\db\interfaces\UuidModelInterface;
use src\app\support\interfaces\HasGuidInterface;

interface PipelineJobItemModelInterface extends HasGuidInterface
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
     * @param string|null $guid
     * @return string
     */
    public function pipelineJobGuid(?string $val = null): string;

    /**
     * Gets the UuidModel for the pipeline item guid
     * @return UuidModelInterface
     */
    public function pipelineJobGuidAsModel(): UuidModelInterface;

    /**
     * Gets the Pipeline Item GUID as bytes for saving to the database in binary
     * @return string
     */
    public function getPipelineJobGuidAsBytes(): string;

    /**
     * Sets the Pipeline Item GUID from bytes coming from the database binary column
     * @param string $bytes
     */
    public function setPipelineJobGuidAsBytes(string $bytes);

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $guid
     * @return string
     */
    public function pipelineItemGuid(?string $val = null): string;

    /**
     * Gets the UuidModel for the pipeline item guid
     * @return UuidModelInterface
     */
    public function pipelineItemGuidAsModel(): UuidModelInterface;

    /**
     * Gets the Pipeline Item GUID as bytes for saving to the database in binary
     * @return string
     */
    public function getPipelineItemGuidAsBytes(): string;

    /**
     * Sets the Pipeline Item GUID from bytes coming from the database binary column
     * @param string $bytes
     */
    public function setPipelineItemGuidAsBytes(string $bytes);

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
