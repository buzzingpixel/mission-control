<?php

declare(strict_types=1);

namespace src\app\tickets\models;

use corbomite\db\traits\UuidTrait;
use corbomite\user\interfaces\UserModelInterface;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use src\app\tickets\interfaces\TicketModelContract;
use src\app\tickets\interfaces\TicketThreadItemModelContract;

class TicketThreadItemModel implements TicketThreadItemModelContract
{
    private const DATE_TIME_PRECISION_FORMAT = 'Y-m-d\TH:i:s.uP';
    use UuidTrait;

    /** @var TicketModelContract */
    private $ticket;

    public function ticket(?TicketModelContract $val = null) : ?TicketModelContract
    {
        return $this->ticket = $val ?? $this->ticket;
    }

    /** @var UserModelInterface */
    private $user;

    public function user(?UserModelInterface $val = null) : ?UserModelInterface
    {
        return $this->user = $val ?? $this->user;
    }

    /** @var string */
    private $content;

    public function content(?string $val = null) : string
    {
        return $this->content = $val ?? $this->content;
    }

    /** @var DateTimeImmutable */
    private $addedAt;

    public function addedAt(?DateTimeInterface $val = null) : DateTimeInterface
    {
        if (! $val && ! $this->addedAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        }

        if ($val) {
            $val = DateTimeImmutable::createFromFormat(
                self::DATE_TIME_PRECISION_FORMAT,
                $val->format(self::DATE_TIME_PRECISION_FORMAT)
            );

            $val = $val->setTimezone(new DateTimeZone('UTC'));
        }

        return $this->addedAt = $val ?? $this->addedAt;
    }

    /** @var bool */
    private $hasBeenModified = false;

    public function hasBeenModified(?bool $val = null) : bool
    {
        return $this->hasBeenModified = $val ?? $this->hasBeenModified;
    }

    /** @var DateTimeImmutable */
    private $modifiedAt;

    public function modifiedAt(?DateTimeInterface $val = null) : DateTimeInterface
    {
        if (! $val && ! $this->modifiedAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->modifiedAt = $this->addedAt();

            return $this->modifiedAt;
        }

        if ($val) {
            $val = DateTimeImmutable::createFromFormat(
                self::DATE_TIME_PRECISION_FORMAT,
                $val->format(self::DATE_TIME_PRECISION_FORMAT)
            );

            $val = $val->setTimezone(new DateTimeZone('UTC'));
        }

        return $this->modifiedAt = $val ?? $this->modifiedAt;
    }

    public function clearModifiedAt() : void
    {
        $this->modifiedAt = null;
    }
}
