<?php

declare(strict_types=1);

namespace src\app\tickets\interfaces;

use corbomite\user\interfaces\UserModelInterface;
use DateTimeInterface;
use src\app\support\interfaces\HasGuidInterface;

interface TicketThreadItemModelContract extends HasGuidInterface
{
    public function ticket(?TicketModelContract $val = null) : ?TicketModelContract;

    public function user(?UserModelInterface $val = null) : ?UserModelInterface;

    public function content(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     */
    public function addedAt(?DateTimeInterface $val = null) : DateTimeInterface;

    public function hasBeenModified(?bool $val = null) : bool;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     */
    public function modifiedAt(?DateTimeInterface $val = null) : DateTimeInterface;
}
