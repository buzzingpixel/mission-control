<?php

declare(strict_types=1);

namespace src\app\tickets\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\tickets\exceptions\InvalidModel;

interface TicketApiContract
{
    public function createModel() : TicketModelContract;

    public function createThreadItemModel() : TicketThreadItemModelContract;

    public function uuidToBytes(string $string) : string;

    public function makeQueryModel() : QueryModelInterface;

    /**
     * @throws InvalidModel
     */
    public function save(TicketModelContract $model) : void;

    /**
     * @throws InvalidModel
     */
    public function saveThreadItem(TicketThreadItemModelContract $model) : void;

    public function fetchOne(?QueryModelInterface $params = null) : ?TicketModelContract;

    /**
     * @return TicketModelContract[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array;

    public function fetchOneThreadItem(?QueryModelInterface $params = null) : ?TicketThreadItemModelContract;

    /**
     * @return TicketThreadItemModelContract[]
     */
    public function fetchAllThreadItems(?QueryModelInterface $params = null) : array;
}
