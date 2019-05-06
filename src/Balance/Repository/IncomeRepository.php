<?php

namespace App\Balance\Repository;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class IncomeRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Income::class);
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

    public function findByAuthorAndFilters(int $authorId, array $filters, bool $count = false)
    {
        $query = $this->createQueryBuilder('i');

        $query->where('i.author = :authorId')
            ->setParameter('authorId', $authorId);

        if (!empty($filters['amount'])) {
            $query->andWhere('i.amount = :amount')
                ->setParameter('amount', $filters['amount']);
        }

        if (isset($filters['minAmount'])) {
            $query->andWhere('i.amount >= :minAmount')
                ->setParameter('minAmount', $filters['minAmount']);
        }

        if (isset($filters['maxAmount'])) {
            $query->andWhere('i.amount <= :maxAmount')
                ->setParameter('maxAmount', $filters['maxAmount']);
        }

        if (!empty($filters['minCreated'])) {
            $query->andWhere('i.created >= :minCreated')
                ->setParameter('minCreated', (new \DateTime($filters['minCreated']))->format('Y-m-d H:i:s'));
        }

        if (!empty($filters['maxCreated'])) {
            $filters['maxCreated'] = (new \DateTime($filters['maxCreated']))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $query->andWhere('i.created <= :maxCreated')
                ->setParameter('maxCreated', $filters['maxCreated']);
        }

        if (!empty($filters['type'])) {
            $query->andWhere('i.type = :typeId')
                ->setParameter('typeId', $filters['type']);
        }

        if ($count) {
            return $query->select('COUNT(i.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        $query->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit']);

        return $query->getQuery()->getResult();
    }
}
