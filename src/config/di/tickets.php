<?php

declare(strict_types=1);

use src\app\tickets\interfaces\TicketApiContract;
use src\app\tickets\services\FetchTicketService;
use src\app\tickets\services\SaveTicketService;
use src\app\tickets\services\SaveTicketThreadItemService;
use src\app\tickets\TicketApi;
use function DI\autowire;

return [
    FetchTicketService::class => autowire(),
    SaveTicketService::class => autowire(),
    SaveTicketThreadItemService::class => autowire(),
    TicketApi::class => autowire(),
    TicketApiContract::class => autowire(TicketApi::class),
];
