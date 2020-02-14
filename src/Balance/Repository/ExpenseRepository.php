<?php

namespace App\Balance\Repository;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

final class ExpenseRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
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

    public function findOneById(UuidInterface $id): ?Expense
    {
        return $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByAuthorAndFilters(UuidInterface $authorId, array $filters, bool $count = false)
    {
        $query = $this->createQueryBuilder('e');

        $query->where('e.author = :authorId')
            ->setParameter('authorId', $authorId);

        if (!empty($filters['amount'])) {
            $query->andWhere('e.amount = :amount')
                ->setParameter('amount', $filters['amount']);
        }

        if (isset($filters['minAmount'])) {
            $query->andWhere('e.amount >= :minAmount')
                ->setParameter('minAmount', $filters['minAmount']);
        }

        if (isset($filters['maxAmount'])) {
            $query->andWhere('e.amount <= :maxAmount')
                ->setParameter('maxAmount', $filters['maxAmount']);
        }

        if (!empty($filters['minCreated'])) {
            $query->andWhere('e.created >= :minCreated')
                ->setParameter('minCreated', (new \DateTime($filters['minCreated']))->format('Y-m-d H:i:s'));
        }

        if (!empty($filters['maxCreated'])) {
            $filters['maxCreated'] = (new \DateTime($filters['maxCreated']))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $query->andWhere('e.created <= :maxCreated')
                ->setParameter('maxCreated', $filters['maxCreated']);
        }

        if (!empty($filters['category'])) {
            $query->andWhere('e.category = :categoryId')
                ->setParameter('categoryId', $filters['category']);
        }

        if ($count) {
            return $query->select('COUNT(e.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        $query->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit']);

        return $query->getQuery()->getResult();
    }
}
