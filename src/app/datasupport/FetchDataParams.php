<?php
declare(strict_types=1);

namespace src\app\datasupport;

class FetchDataParams implements FetchDataParamsInterface
{
    private $limit = 0;

    public function limit(?int $limit = null): int
    {
        return $this->limit = $limit !== null ? $limit : $this->limit;
    }

    private $offset = 0;

    public function offset(?int $offset = null): int
    {
        return $this->offset = $offset !== null ? $offset : $this->offset;
    }

    private $order = [];

    public function order(?array $order = null): array
    {
        return $this->order = $order !== null ? $order : $this->order;
    }

    public function addOrder(string $col, string $dir = 'desc')
    {
        $dir = $dir === 'desc' ? 'desc' : 'asc';
        $this->order[$col] = $dir;
    }

    private $whereKey = 0;
    private $where = [];

    public function where(): array
    {
        return $this->where;
    }

    public function addWhere(string $col, $val, string $comparison = '=', bool $or = false)
    {
        $this->where[$this->whereKey]['wheres'][] = [
            'col' => $col,
            'val' => $val,
            'comparison' => $comparison,
            'operator' => $or ? 'OR' : 'AND',
        ];
    }

    public function addWhereGroup(bool $or = true)
    {
        $this->whereKey++;
        $this->where[$this->whereKey]['operator'] = $or ? 'OR' : 'AND';
        $this->where[$this->whereKey]['wheres'] = [];
    }
}
