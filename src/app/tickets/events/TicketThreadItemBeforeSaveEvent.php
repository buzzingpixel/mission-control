<?php

declare(strict_types=1);

namespace src\app\tickets\events;

use corbomite\events\interfaces\EventInterface;
use src\app\tickets\interfaces\TicketThreadItemModelContract;
use src\app\tickets\TicketApi;

class TicketThreadItemBeforeSaveEvent implements EventInterface
{
    /** @var TicketThreadItemModelContract */
    private $model;
    /** @var bool */
    private $new;

    public function __construct(
        TicketThreadItemModelContract $model,
        bool $new = false
    ) {
        $this->model = $model;
        $this->new   = $new;
    }

    public function new() : bool
    {
        return $this->new;
    }

    public function model() : TicketThreadItemModelContract
    {
        return $this->model;
    }

    public function provider() : string
    {
        return TicketApi::class;
    }

    public function name() : string
    {
        return 'TicketThreadItemBeforeSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
