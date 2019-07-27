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
use function in_array;

class TicketModel implements TicketModelContract
{
    use UuidTrait;

    private const DATE_TIME_PRECISION_FORMAT = 'Y-m-d\TH:i:s.uP';

    /** @var UserModelInterface */
    private $createdByUser;

    public function createdByUser(?UserModelInterface $val = null) : ?UserModelInterface
    {
        return $this->createdByUser = $val ?? $this->createdByUser;
    }

    public function clearCreatedBy() : void
    {
        $this->createdByUser = null;
    }

    /** @var UserModelInterface */
    private $assignedToUser;

    public function assignedToUser(?UserModelInterface $val = null) : ?UserModelInterface
    {
        return $this->assignedToUser = $val ?? $this->assignedToUser;
    }

    public function clearAssignedTo() : void
    {
        $this->assignedToUser = null;
    }

    /** @var string */
    private $title = '';

    public function title(?string $val = null) : string
    {
        return $this->title = $val ?? $this->title;
    }

    /** @var string */
    private $content = '';

    public function content(?string $val = null) : string
    {
        return $this->content = $val ?? $this->content;
    }

    /** @var string */
    private $status = 'new';

    /**
     * @param string $val Enum: new, in_progress, on_hold, resolved
     */
    public function status(?string $val = null) : string
    {
        if ($val === null || ! in_array($val, [
            'new',
            'in_progress',
            'on_hold',
            'resolved',
        ])) {
            return $this->status;
        }

        return $this->status = $val;
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

    /** @var DateTimeImmutable */
    private $resolvedAt;

    public function resolvedAt(?DateTimeInterface $val = null) : ?DateTimeInterface
    {
        if (! $val && ! $this->resolvedAt) {
            return null;
        }

        if ($val) {
            $val = DateTimeImmutable::createFromFormat(
                self::DATE_TIME_PRECISION_FORMAT,
                $val->format(self::DATE_TIME_PRECISION_FORMAT)
            );

            $val = $val->setTimezone(new DateTimeZone('UTC'));
        }

        return $this->resolvedAt = $val ?? $this->resolvedAt;
    }

    /** @var TicketThreadItemModelContract[] */
    private $threadItems = [];

    /**
     * @param TicketThreadItemModelContract[]|null $val
     *
     * @return TicketThreadItemModelContract[]
     */
    public function threadItems(?array $val = null) : array
    {
        if ($val === null) {
            return $this->threadItems;
        }

        $this->threadItems = [];

        foreach ($val as $model) {
            $this->addThreadItem($model);
        }

        return $this->threadItems;
    }

    public function addThreadItem(TicketThreadItemModelContract $val) : void
    {
        $this->threadItems[] = $val;
    }

    /** @var UserModelInterface[] */
    private $watchers = [];

    /**
     * @param UserModelInterface[]|null $val
     *
     * @return UserModelInterface[]
     */
    public function watchers(?array $val = null) : array
    {
        if ($val === null) {
            return $this->watchers;
        }

        $this->watchers = [];

        foreach ($val as $model) {
            $this->addWatcher($model);
        }

        return $this->watchers;
    }

    public function addWatcher(?UserModelInterface $val) : void
    {
        $this->watchers[] = $val;
    }

    public function removeWatcher(?UserModelInterface $val) : void
    {
        $setModels = [];

        foreach ($this->watchers as $model) {
            if ($model->guid() === $val->guid()) {
                continue;
            }

            $setModels[] = $model;
        }

        $this->watchers = $setModels;
    }
}
