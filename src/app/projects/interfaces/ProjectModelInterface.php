<?php
declare(strict_types=1);

namespace src\app\projects\interfaces;

use DateTime;

interface ProjectModelInterface
{
    /**
     * Constructor accepts array of properties to set on the model
     * @param array $props
     */
    public function __construct(array $props = []);

    /**
     * Returns the value of guid. Sets value if incoming argument is set
     * @param string|null $guid
     * @return string
     */
    public function guid(?string $guid = null): string;

    /**
     * Returns the value of isActive. Sets value if incoming argument is set
     * @param bool|null $isActive
     * @return bool
     */
    public function isActive(?bool $isActive = null): bool;

    /**
     * Returns the value of title. Sets value if incoming argument is set
     * @param string|null $title
     * @return string
     */
    public function title(?string $title = null): string;

    /**
     * Returns the value of slug. Sets value if incoming argument is set
     * @param string|null $slug
     * @return string
     */
    public function slug(?string $slug = null): string;

    /**
     * Returns the value of description. Sets value if incoming argument is set
     * @param string|null $description
     * @return string
     */
    public function description(?string $description = null): string;

    /**
     * Returns the value of addedAt. Sets value if incoming argument is set.
     * If no DateTime has been set, it should return the current DateTime.
     * The constructor is probably the appropriate place to set initial value
     * @param DateTime|null $addedAt
     * @return DateTime
     */
    public function addedAt(?DateTime $addedAt = null): DateTime;
}
