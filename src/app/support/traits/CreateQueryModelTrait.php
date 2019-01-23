<?php
declare(strict_types=1);

namespace src\app\support\traits;

use corbomite\di\Di;
use corbomite\db\Factory as DbFactory;
use corbomite\db\interfaces\QueryModelInterface;

/**
 * Trait QueryModelTrait
 * @property Di $di Make sure this property is available/injected on implementing class
 */
trait CreateQueryModelTrait
{
    public function makeQueryModel(): QueryModelInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->di->getFromDefinition(DbFactory::class)->makeQueryModel();
    }
}
