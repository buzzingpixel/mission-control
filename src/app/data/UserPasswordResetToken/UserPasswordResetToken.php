<?php
declare(strict_types=1);

namespace src\app\data\UserPasswordResetToken;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method UserPasswordResetTokenTable getTable()
 * @method UserPasswordResetTokenRelationships getRelationships()
 * @method UserPasswordResetTokenRecord|null fetchRecord($primaryVal, array $with = [])
 * @method UserPasswordResetTokenRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method UserPasswordResetTokenRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method UserPasswordResetTokenRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method UserPasswordResetTokenRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method UserPasswordResetTokenRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method UserPasswordResetTokenSelect select(array $whereEquals = [])
 * @method UserPasswordResetTokenRecord newRecord(array $fields = [])
 * @method UserPasswordResetTokenRecord[] newRecords(array $fieldSets)
 * @method UserPasswordResetTokenRecordSet newRecordSet(array $records = [])
 * @method UserPasswordResetTokenRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method UserPasswordResetTokenRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class UserPasswordResetToken extends Mapper
{
}
