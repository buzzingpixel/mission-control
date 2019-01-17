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

    private $where = [];

    public function where(?array $where = null): array
    {
        return $this->where = $where !== null ? $where : $this->where;
    }

    public function addWhere(string $col, $val, string $operator = '=', bool $or = false)
    {
        if ($or) {
            $this->where[][$col . ' ' . $operator . ' '] = $val;
            return;
        }

        $this->where[\count($this->where) - 2][$col . ' ' . $operator . ' '] = $val;
    }
}
