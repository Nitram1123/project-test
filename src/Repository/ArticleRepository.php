<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Paginator\PaginatorDTOInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository implements ArticleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function getSearchQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('a');
    }

    public function search(PaginatorDTOInterface $paginatorDTO): void
    {
        $qb = $this->getSearchQueryBuilder();
        $qb
            ->setFirstResult($paginatorDTO->getFirstResult())
            ->setMaxResults($paginatorDTO->getNumberByPage())
            ->addOrderBy('a.updatedAt', 'DESC');
        $paginator = new Paginator($qb);
        $paginator->getFetchJoinCollection();
        $data = \iterator_to_array($paginator->getIterator());

        $paginatorDTO
            ->setNumberTotalItem(\count($paginator))
            ->setNumberPage((int) (\ceil(\count($paginator) / $paginatorDTO->getNumberByPage()) ?: 1))
            ->setData($data);
    }

    public function searchWithId(string $id): Article|null
    {
        return $this->find($id);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
