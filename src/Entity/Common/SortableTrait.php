<?php

declare(strict_types=1);

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;

trait SortableTrait
{
    /**
     * Lower values should be first.
     */
    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    protected int $sortOrder = 0;

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
