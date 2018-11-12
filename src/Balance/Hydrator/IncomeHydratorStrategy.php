<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use Doctrine\ORM\EntityManagerInterface;

class IncomeHydratorStrategy implements HydrationStrategyInterface
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