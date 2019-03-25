<?php

namespace App\Application\Service;

class Paginator implements PaginatorInterface
{
    private $page;

    private $limit;

    private $offset;

    public function __construct($page, $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function setPage(int $page): PaginatorInterface
    {
        $this->page = ($page > 0) ? $page : 1;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setLimit(int $limit): PaginatorInterface
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    private function calculateOffset(): int
    {
        $this->offset = ($this->page !== 1) ? (($this->page - 1) * $this->limit) : 0;

        return $this->offset;
    }

    public function getOffset(): int
    {
        return $this->calculateOffset();
    }

    public function calculateLastPage(int $count): int
    {
        return ($count % $this->limit) ? (($count / $this->limit) + 1) : ($count / $this->limit);
    }

    public function isFirstPage(): bool
    {
        return ($this->page === 1);
    }

    public function isLastPage(int $lastPage): bool
    {
        return ($this->page === $lastPage);
    }

    public function nextPage(): int
    {
        return ($this->page + 1);
    }

    public function previousPage(): int
    {
        return ($this->page - 1);
    }

    public function isPageOutOfRange(int $page, int $lastPage): bool
    {
        return ($page < 1) || ($page > $lastPage && $lastPage > 0);
    }
}
