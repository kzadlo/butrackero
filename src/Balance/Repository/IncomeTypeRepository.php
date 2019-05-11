<?php

namespace App\Balance\Repository;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\IncomeType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class IncomeTypeRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IncomeType::class);
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

    public function findOneById(UuidInterface $id): ?IncomeType
    {
        return $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByAuthorAndFilters(UuidInterface $authorId, array $filters, bool $count = false)
    {
        $query = $this->createQueryBuilder('t');

        $query->where('t.author = :authorId')
            ->setParameter('authorId', $authorId);

        if (!empty($filters['name'])) {
            $query->andWhere('t.name = :name')
                ->setParameter('name', $filters['name']);
        }

        if (isset($filters['description'])) {
            $query->andWhere('t.description LIKE :description')
                ->setParameter('description', '%'.$filters['description'].'%');
        }

        if ($count) {
            return $query->select('COUNT(t.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        $query->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit']);

        return $query->getQuery()->getResult();
    }
}
