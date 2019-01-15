<?php
declare(strict_types=1);

namespace src\app\data\FlashDatum;

use Atlas\Mapper\Record;

/**
 * @method FlashDatumRow getRow()
 */
class FlashDatumRecord extends Record
{
    use FlashDatumFields;
}
