<?php
declare(strict_types=1);

namespace src\app\datasupport;

interface FetchDataParamsInterface
{
    /**
     * Returns the value of limit. Sets value if incoming argument is set
     * @param int|null $limit
     * @return int
     */
    public function limit(?int $limit = null): int;

    /**
     * Returns the value of offset. Sets value if incoming argument is set
     * @param int|null $offset
     * @return int
     */
    public function offset(?int $offset = null): int;

    /**
     * Gets an array of order params.  Sets value if incoming argument is set.
     * The array should be formatted as:
     * [
     *     'col_name' => 'sort_dir',
     *     'another_col_name' => 'sort_dir',
     * ]
     * @return array
     */
    public function order(?array $order = null): array;

    /**
     * Adds order param
     * @param string $col
     * @param string $dir Should be only 'desc' or 'asc'
     */
    public function addOrder(string $col, string $dir = 'desc');

    /**
     * Returns where params. Sets value if incoming argument is set.
     * The array should be formatted as (each key in the top array represents OR):
     * [
     *     [
     *         'col_name >' => 'some_val',
     *         'col_name <' => 'some_val',
     *         'col_name =' => 'some_val',
     *         'col_name !=' => 'some_val',
     *     ]
     * ]
     * @return array
     */
    public function where(?array $where = null): array;

    /**
     * Adds a where param
     * @param string $col
     * @param mixed $val
     * @param string $operator
     * @param bool $or
     * @return mixed
     */
    public function addWhere(string $col, $val, string $operator = '=', bool $or = false);
}
