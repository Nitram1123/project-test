<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Paginator\PaginatorDTOInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getSearchQueryBuilder(string|null $article, string|null $parent): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        if ($article !== null) {
            $qb
                ->leftJoin('c.article', 'a')
                ->where('a.id = :article_id')
                ->setParameter('article_id', $article);
        }

        if ($parent !== null) {
            $qb
                ->leftJoin('c.parent', 'p')
                ->where('p.id = :parent_id')
                ->setParameter('parent_id', $parent);
        }

        return $qb;
    }

    public function search(PaginatorDTOInterface $paginatorDTO, string|null $article, string|null $parent): void
    {
        $qb = $this->getSearchQueryBuilder($article, $parent);
        $qb
            ->setFirstResult($paginatorDTO->getFirstResult())
            ->setMaxResults($paginatorDTO->getNumberByPage())
            ->addOrderBy('c.createdAt', 'ASC');
        $paginator = new Paginator($qb);
        $paginator->getFetchJoinCollection();
        $data = \iterator_to_array($paginator->getIterator());

        $paginatorDTO
            ->setNumberTotalItem(\count($paginator))
            ->setNumberPage((int) (\ceil(\count($paginator) / $paginatorDTO->getNumberByPage()) ?: 1))
            ->setData($data);
    }

    public function searchWithId(string $id): Comment|null
    {
        return $this->find($id);
    }

    public function add(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
