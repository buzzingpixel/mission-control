<?php

declare(strict_types=1);

namespace src\app\tickets;

use src\app\support\traits\MakeQueryModelTrait;
use src\app\support\traits\UuidToBytesTrait;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use src\app\tickets\models\TicketModel;
use src\app\tickets\models\TicketThreadItemModel;

class TicketApi implements interfaces\TicketApiContract
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    public function createModel() : TicketModelContract
    {
        return new TicketModel();
    }

    public function createThreadItemModel() : TicketThreadItemModelContract
    {
        return new TicketThreadItemModel();
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
