<?php

declare(strict_types=1);

namespace App\Paginator;

use Assert\Assertion;
use Symfony\Component\Serializer\Annotation\Groups;

class PaginatorDTO implements PaginatorDTOInterface
{
    #[Groups(['paginator'])]
    private int|null $numberTotalItem = null;
    #[Groups(['paginator'])]
    private int|null $numberPage      = null;

    /** @var array<int, mixed> */
    #[Groups(['paginator'])]
    private array $data = [];

    public function __construct(
        #[Groups(['paginator'])]
        private int $page,
        #[Groups(['paginator'])]
        private int $numberByPage,
    ) {
        Assertion::min($page, 1);
        Assertion::range($numberByPage, 1, 100);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getNumberByPage(): int
    {
        return $this->numberByPage;
    }

    public function getFirstResult(): int
    {
        return ($this->page - 1) * $this->numberByPage;
    }

    public function getNumberTotalItem(): int|null
    {
        return $this->numberTotalItem;
    }

    public function setNumberTotalItem(int $numberTotalItem): self
    {
        $this->numberTotalItem = $numberTotalItem;

        return $this;
    }

    public function getNumberPage(): int|null
    {
        return $this->numberPage;
    }

    public function setNumberPage(int $numberPage): self
    {
        $this->numberPage = $numberPage;

        return $this;
    }

    /** {@inheritdoc} */
    public function getData(): array
    {
        return $this->data;
    }

    /** {@inheritdoc} */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
