<?php

namespace App\Hydrator;

use App\Entity\BalanceEntityInterface;
use App\Entity\Income;
use App\Entity\IncomeType;
use Doctrine\ORM\EntityManagerInterface;

class IncomeHydratorStrategy extends HydrationStrategy
{
    /** @var Income $entity */
    public function extract(BalanceEntityInterface $entity): array
    {
        return [
            'id' => $entity->getId(),
            'amount' => $entity->getAmount(),
            'category' => $entity->getType()->getName(),
            'created_timestamp' => $entity->getCreated()->getTimestamp()
        ];
    }

    public function hydrate(array $data, EntityManagerInterface $entityManager): BalanceEntityInterface
    {
        return (new Income())
            ->setAmount($data['amount'])
            ->setType($entityManager->find(IncomeType::class, $data['type']));
    }
}