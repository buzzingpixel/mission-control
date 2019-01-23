<?php
declare(strict_types=1);

namespace src\app\pings;

use corbomite\di\Di;
use src\app\pings\models\PingModel;
use src\app\support\traits\UuidToBytesTrait;
use src\app\pings\interfaces\PingApiInterface;
use src\app\support\traits\MakeQueryModelTrait;
use corbomite\db\interfaces\QueryModelInterface;
use src\app\pings\interfaces\PingModelInterface;

class PingApi implements PingApiInterface
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function createModel(): PingModelInterface
    {
        return new PingModel();
    }

    public function save(PingModelInterface $model): void
    {
        // TODO: Implement save() method.
    }

    public function archive(PingModelInterface $model): void
    {
        // TODO: Implement archive() method.
    }

    public function unArchive(PingModelInterface $model): void
    {
        // TODO: Implement unArchive() method.
    }

    public function delete(PingModelInterface $model)
    {
        // TODO: Implement delete() method.
    }

    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?PingModelInterface {
        // TODO: Implement fetchOne() method.
    }

    public function fetchAll(?QueryModelInterface $params = null): array
    {
        // TODO: Implement fetchAll() method.
    }
}
