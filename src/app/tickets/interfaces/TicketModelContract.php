<?php

declare(strict_types=1);

namespace src\app\tickets\interfaces;

use corbomite\user\interfaces\UserModelInterface;
use DateTimeInterface;
use src\app\support\interfaces\HasGuidInterface;

interface TicketModelContract extends HasGuidInterface
{
    public function createdByUser(?UserModelInterface $val = null) : ?UserModelInterface;

    public function clearCreatedBy() : void;

    public function assignedToUser(?UserModelInterface $val = null) : ?UserModelInterface;

    public function clearAssignedTo() : void;

    public function title(?string $val = null) : string;

    public function content(?string $val = null) : string;

    /**
     * @param string $val Enum: new, in_progress, on_hold, resolved
     */
    public function status(?string $val = null) : string;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     */
    public function addedAt(?DateTimeInterface $val = null) : DateTimeInterface;

    /**
     * Returns the value. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     */
    public function resolvedAt(?DateTimeInterface $val = null) : DateTimeInterface;

    /**
     * @param TicketThreadItemModelContract[]|null $val
     *
     * @return TicketThreadItemModelContract[]
     */
    public function threadItems(?array $val = null) : array;

    public function addThreadItem(TicketThreadItemModelContract $val) : void;

    /**
     * @param UserModelInterface[]|null $val
     *
     * @return UserModelInterface[]
     */
    public function watchers(?array $val = null) : array;

    public function addWatcher(?UserModelInterface $val) : void;

    public function removeWatcher(?UserModelInterface $val) : void;
}
