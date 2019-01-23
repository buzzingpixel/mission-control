<?php
declare(strict_types=1);

namespace src\app\support\traits;

use corbomite\db\Factory as DbFactory;
use corbomite\db\interfaces\QueryModelInterface;

trait MakeQueryModelTrait
{
    public function makeQueryModel(): QueryModelInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new DbFactory())->makeQueryModel();
    }
}
