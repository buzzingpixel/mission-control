<?php
declare(strict_types=1);

namespace src\app\data\FlashDatum;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method FlashDatumTable getTable()
 * @method FlashDatumRelationships getRelationships()
 * @method FlashDatumRecord|null fetchRecord($primaryVal, array $with = [])
 * @method FlashDatumRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method FlashDatumRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method FlashDatumRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method FlashDatumRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method FlashDatumRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method FlashDatumSelect select(array $whereEquals = [])
 * @method FlashDatumRecord newRecord(array $fields = [])
 * @method FlashDatumRecord[] newRecords(array $fieldSets)
 * @method FlashDatumRecordSet newRecordSet(array $records = [])
 * @method FlashDatumRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method FlashDatumRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class FlashDatum extends Mapper
{
}
