<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Paginator\PaginatorDTOInterface;

interface ArticleRepositoryInterface
{
    public function search(PaginatorDTOInterface $paginatorDTO): void;

    public function searchWithId(string $id): Article|null;

    public function add(Article $entity, bool $flush = false): void;

    public function remove(Article $entity, bool $flush = false): void;
}
