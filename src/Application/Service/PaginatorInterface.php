<?php

namespace App\Application\Service;

interface PaginatorInterface
{
    public function setPage(int $page): PaginatorInterface;

    public function getPage(): int;

    public function setLimit(int $limit): PaginatorInterface;

    public function getLimit(): int;

    public function getOffset(): int;

    public function isFirstPage(): bool;

    public function calculateLastPage(int $count): int;

    public function isLastPage(int $count): bool;

    public function nextPage(): int;

    public function previousPage(): int;

    public function isPageOutOfRange(int $page, int $lastPage): bool;
}
