<?php

declare(strict_types=1);

namespace src\app\tickets\events;

use corbomite\events\interfaces\EventInterface;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\TicketApi;

class TicketAfterSaveEvent implements EventInterface
{
    /** @var TicketModelContract */
    private $model;
    /** @var bool */
    private $new;

    public function __construct(
        TicketModelContract $model,
        bool $new = false
    ) {
        $this->model = $model;
        $this->new   = $new;
    }

    public function new() : bool
    {
        return $this->new;
    }

    public function model() : TicketModelContract
    {
        return $this->model;
    }

    public function provider() : string
    {
        return TicketApi::class;
    }

    public function name() : string
    {
        return 'TicketAfterSave';
    }

    /** @var bool */
    private $stop = false;

    public function stopPropagation(?bool $stop = null) : bool
    {
        return $this->stop = $stop ?? $this->stop;
    }
}
