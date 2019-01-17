<?php
declare(strict_types=1);

namespace src\app\datasupport;

use Atlas\Mapper\MapperSelect;

interface BuildQueryInterface
{
    /**
     * @see build()
     */
    public function __invoke(
        string $select,
        FetchDataParamsInterface $fetchDataParams
    ): MapperSelect;

    /**
     * Builds the Atlas query
     * @param string $select Should be a \Atlas\Mapper\Mapper class name string
     * @param FetchDataParamsInterface $fetchDataParams
     * @return MapperSelect
     */
    public function build(
        string $select,
        FetchDataParamsInterface $fetchDataParams
    ): MapperSelect;
}
