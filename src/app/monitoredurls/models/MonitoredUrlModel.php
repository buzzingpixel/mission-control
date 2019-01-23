<?php
declare(strict_types=1);

namespace src\app\monitoredurls\models;

use DateTime;
use DateTimeZone;
use src\app\support\traits\ModelErrorsTrait;
use src\app\support\traits\ModelAddedAtTrait;
use src\app\support\traits\StandardModelTrait;
use src\app\monitoredurls\interfaces\MonitoredUrlModelInterface;

class MonitoredUrlModel implements MonitoredUrlModelInterface
{
    use ModelErrorsTrait;
    use ModelAddedAtTrait;
    use StandardModelTrait;

    private $url = '';

    public function url(?string $val = null): string
    {
        return $this->url = $val ?? $this->url;
    }

    /** @var DateTime|null */
    private $checkedAt;

    public function checkedAt(?DateTime $val = null): DateTime
    {
        if (! $val && ! $this->checkedAt) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->checkedAt = new DateTime('now', new DateTimeZone('UTC'));
        }

        return $this->checkedAt = $val ?? $this->checkedAt;
    }
}
