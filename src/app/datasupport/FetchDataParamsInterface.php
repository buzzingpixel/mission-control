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
     * Returns where params.
     * The array should be formatted as:
     * [
     *     [
     *         'wheres' => [
     *             [
     *                 'col' => 'guid',
     *                 'val' => '',
     *                 'comparison' => '!=',
     *                 'operator' => 'AND',
     *             ],
     *         ],
     *     ],
     *     [
     *         'operator' => 'AND',
     *         'wheres' => [
     *             [
     *                 'col' => 'title',
     *                 'val' => 'This is a test',
     *                 'comparison' => '=',
     *                 'operator' => 'AND',
     *             ],
     *             [
     *                 'col' => 'slug',
     *                 'val' => 'this-is-a-test',
     *                 'comparison' => '=',
     *                 'operator' => 'OR',
     *             ],
     *         ],
     *     ],
     * ]
     * @return array
     */
    public function where(): array;

    /**
     * Adds a where param
     * @param string $col
     * @param string|int|float|null|array $val Array should produce WHERE col IN query
     * @param string $operator Any MySQL comparison operator. If val is array,
     *                         builder should convert != into NOT IN, otherwise
     *                         do IN
     * @param bool $or
     * @return mixed
     */
    public function addWhere(string $col, $val, string $comparison = '=', bool $or = false);

    /**
     * Starts a new where group
     * @param bool $or
     * @return mixed
     */
    public function addWhereGroup(bool $or = true);
}
