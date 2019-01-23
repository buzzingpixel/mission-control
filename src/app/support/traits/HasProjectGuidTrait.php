<?php
declare(strict_types=1);

namespace src\app\support\traits;

use corbomite\db\traits\UuidTrait;
use corbomite\db\models\UuidModel;
use corbomite\db\interfaces\UuidModelInterface;

trait HasProjectGuidTrait
{
    use UuidTrait;

    /** @var UuidModelInterface */
    private $projectUuidModel;

    public function projectGuid(?string $guid = null): ?string
    {
        if ($guid !== null) {
            $this->projectUuidModel = new UuidModel($guid);
        }

        if (! $this->projectUuidModel) {
            return null;
        }

        return $this->projectUuidModel->toString();
    }

    public function projectGuidAsModel(): ?UuidModelInterface
    {
        return $this->projectUuidModel;
    }

    public function getProjectGuidAsBytes(): ?string
    {
        if (! $this->projectUuidModel) {
            return null;
        }

        return $this->projectUuidModel->toBytes();
    }

    public function setProjectGuidAsBytes(string $bytes): void
    {
        $this->projectUuidModel = UuidModel::fromBytes($bytes);
    }
}
