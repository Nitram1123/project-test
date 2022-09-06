<?php

declare(strict_types=1);

namespace App\Paginator;

interface PaginatorDTOInterface
{
    public function getPage(): int;

    public function getNumberByPage(): int;

    public function getFirstResult(): int;

    public function getNumberTotalItem(): int|null;

    public function setNumberTotalItem(int $numberTotalItem): self;

    public function getNumberPage(): int|null;

    public function setNumberPage(int $numberPage): self;

    /**
     * @return array<int, mixed>
     */
    public function getData(): array;

    /**
     * @param array<int, mixed> $data
     */
    public function setData(array $data): self;
}
