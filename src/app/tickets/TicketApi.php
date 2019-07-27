<?php

declare(strict_types=1);

namespace src\app\tickets;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use src\app\tickets\models\TicketModel;
use src\app\tickets\models\TicketThreadItemModel;

class TicketApi implements interfaces\TicketApiContract
{
    public function createModel() : TicketModelContract
    {
        return new TicketModel();
    }

    public function createThreadItemModel() : TicketThreadItemModelContract
    {
        return new TicketThreadItemModel();
    }

    public function uuidToBytes(string $string) : string
    {
        // TODO: Implement uuidToBytes() method.
    }

    public function makeQueryModel() : QueryModelInterface
    {
        // TODO: Implement makeQueryModel() method.
    }

    /**
     * @throws InvalidModel
     */
    public function save(TicketModelContract $model) : void
    {
        // TODO: Implement save() method.
    }

    public function saveThreadItem(TicketThreadItemModelContract $model) : void
    {
        // TODO: Implement saveThreadItem() method.
    }
}
