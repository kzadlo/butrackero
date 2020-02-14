<?php

namespace App\Balance\Repository;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\ExpenseCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

final class ExpenseCategoryRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseCategory::class);
    }

    public function save(BalanceEntityInterface $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    public function delete(BalanceEntityInterface $entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    public function findOneById(UuidInterface $id): ?ExpenseCategory
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByAuthorAndFilters(UuidInterface $authorId, array $filters, bool $count = false)
    {
        $query = $this->createQueryBuilder('c');

        $query->where('c.author = :authorId')
            ->setParameter('authorId', $authorId);

        if (!empty($filters['name'])) {
            $query->andWhere('c.name = :name')
                ->setParameter('name', $filters['name']);
        }

        if (isset($filters['description'])) {
            $query->andWhere('c.description LIKE :description')
                ->setParameter('description', '%'.$filters['description'].'%');
        }

        if ($count) {
            return $query->select('COUNT(c.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        $query->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit']);

        return $query->getQuery()->getResult();
    }
}
