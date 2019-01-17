<?php
declare(strict_types=1);

namespace src\app\datasupport;

use Atlas\Mapper\MapperSelect;
use corbomite\db\Factory as OrmFactory;

class BuildQuery implements BuildQueryInterface
{
    private $ormFactory;

    public function __construct(OrmFactory $ormFactory)
    {
        $this->ormFactory = $ormFactory;
    }

    public function __invoke(
        string $select,
        FetchDataParamsInterface $fetchDataParams
    ): MapperSelect {
        return $this->build($select, $fetchDataParams);
    }

    public function build(
        string $select,
        FetchDataParamsInterface $fetchDataParams
    ): MapperSelect {
        $query = $this->ormFactory->makeOrm()->select($select);

        if ($limit = $fetchDataParams->limit()) {
            $query->limit($limit);
        }

        if ($offset = $fetchDataParams->offset()) {
            $query->offset($offset);
        }

        foreach ($fetchDataParams->order() as $col => $dir) {
            $query->orderBy($col . ' ' . $dir);
        }

        $firstWhere = true;

        foreach ($fetchDataParams->where() as $where) {
            if ($firstWhere) {
                $query->where('(');
            }

            if (! $firstWhere) {
                $query->orWhere('(');
            }

            foreach ($where as $col => $val) {
                // TODO: check for array and do an WHERE IN statement
                $query->catWhere($col, $val);
            }

            $query->catWhere(')');
        }

        return $query;
    }
}
