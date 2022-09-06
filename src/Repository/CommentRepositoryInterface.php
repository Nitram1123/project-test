<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Paginator\PaginatorDTOInterface;

interface CommentRepositoryInterface
{
    public function search(PaginatorDTOInterface $paginatorDTO, string|null $article, string|null $parent): void;

    public function searchWithId(string $id): Comment|null;

    public function add(Comment $entity, bool $flush = false): void;

    public function remove(Comment $entity, bool $flush = false): void;
}
