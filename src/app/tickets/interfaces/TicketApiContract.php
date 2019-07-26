<?php

declare(strict_types=1);

namespace src\app\tickets\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\tickets\exceptions\InvalidModel;

interface TicketApiContract
{
    public function createModel() : TicketModelContract;

    public function uuidToBytes(string $string) : string;

    public function makeQueryModel() : QueryModelInterface;

    /**
     * @throws InvalidModel
     */
    public function save(TicketModelContract $model) : void;

    public function saveThreadItem(TicketThreadItemModelContract $model) : void;
}
