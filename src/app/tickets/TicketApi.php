<?php

declare(strict_types=1);

namespace src\app\tickets;

use Psr\Container\ContainerInterface;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\support\traits\UuidToBytesTrait;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use src\app\tickets\models\TicketModel;
use src\app\tickets\models\TicketThreadItemModel;
use src\app\tickets\services\SaveTicketService;
use src\app\tickets\services\SaveTicketThreadItemService;

class TicketApi implements interfaces\TicketApiContract
{
    use UuidToBytesTrait;
    use MakeQueryModelTrait;

    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

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
        $service = $this->di->get(SaveTicketService::class);
        $service->save($model);
    }

    public function saveThreadItem(TicketThreadItemModelContract $model) : void
    {
        $service = $this->di->get(SaveTicketThreadItemService::class);
        $service->save($model);
    }
}
