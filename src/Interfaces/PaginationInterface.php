<?php

namespace App\Interfaces;

interface PaginationInterface
{
    public function getNextPage(): int;
    public function getPreviousPage(): int;

    public function getCurrentPage(): int;
    public function setCurrentPage(int $currentPage);

    public function getMaxPage(): int;
    public function setMaxPage(int $maxPage);

    public function isPreviousPageDisabled(): bool;
    public function isNextPageDisabled(): bool;

    public function getFirstPage(): int;
    public function getLastPage(): int;
}
