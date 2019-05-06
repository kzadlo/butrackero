<?php

namespace App\Application\Service;

class Filter
{
    private $filters = [];

    public function prepare(array $filters): void
    {
        foreach ($filters as $key => $filter) {
            $this->add($key, $filter);
        }
    }

    public function get(string $key): string
    {
        return $this->filters[$key];
    }

    public function add(string $key, string $filter): void
    {
        $this->filters[$key] = $filter;
    }

    public function remove(string $key): void
    {
        if ($this->hasFilter($key)) {
            unset($this->filters[$key]);
        }
    }

    public function getAll(): array
    {
        return $this->filters;
    }

    public function hasFilter(string $key): bool
    {
        return array_key_exists($key, $this->filters);
    }

    public function hasFilters(): bool
    {
        return !empty($this->filters);
    }
}
