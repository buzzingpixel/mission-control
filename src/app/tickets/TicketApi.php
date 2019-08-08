<?php

declare(strict_types=1);

namespace src\app\tickets;

use corbomite\db\interfaces\QueryModelInterface;
use Psr\Container\ContainerInterface;
use src\app\support\traits\MakeQueryModelTrait;
use src\app\support\traits\UuidToBytesTrait;
use src\app\tickets\exceptions\InvalidModel;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use src\app\tickets\models\TicketModel;
use src\app\tickets\models\TicketThreadItemModel;
use src\app\tickets\services\CountAllTickets;
use src\app\tickets\services\FetchThreadItemsService;
use src\app\tickets\services\FetchTicketService;
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

    /**
     * @throws InvalidModel
     */
    public function saveThreadItem(TicketThreadItemModelContract $model) : void
    {
        $service = $this->di->get(SaveTicketThreadItemService::class);
        $service->save($model);
    }

    /** @var ?int */
    private $limit;

    public function fetchOne(?QueryModelInterface $params = null) : ?TicketModelContract
    {
        $this->limit = 1;
        $result      = $this->fetchAll($params)[0] ?? null;
        $this->limit = null;

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(?QueryModelInterface $params = null) : array
    {
        $service = $this->di->get(FetchTicketService::class);

        $params = $params ?? $this->makeQueryModel();

        if ($this->limit) {
            $params->limit($this->limit);
        }

        return $service->fetch($params);
    }

    public function countAll(?QueryModelInterface $params = null) : int
    {
        $service = $this->di->get(CountAllTickets::class);

        $params = $params ?? $this->makeQueryModel();

        return $service->count($params);
    }

    /** @var ?int */
    private $threadItemsLimit;

    public function fetchOneThreadItem(?QueryModelInterface $params = null) : ?TicketThreadItemModelContract
    {
        $this->limit            = 1;
        $result                 = $this->fetchAllThreadItems($params)[0] ?? null;
        $this->threadItemsLimit = null;

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function fetchAllThreadItems(?QueryModelInterface $params = null) : array
    {
        $service = $this->di->get(FetchThreadItemsService::class);

        $params = $params ?? $this->makeQueryModel();

        if ($this->threadItemsLimit) {
            $params->limit($this->threadItemsLimit);
        }

        return $service->fetch($params);
    }
}
