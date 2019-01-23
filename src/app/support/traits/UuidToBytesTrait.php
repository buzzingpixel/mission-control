<?php
declare(strict_types=1);

namespace src\app\support\traits;

use corbomite\db\models\UuidModel;

trait UuidToBytesTrait
{
    public function uuidToBytes(string $string): string
    {
        return (new UuidModel($string))->toBytes();
    }
}
