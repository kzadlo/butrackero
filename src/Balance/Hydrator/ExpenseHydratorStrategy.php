<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseHydratorStrategy implements HydrationStrategyInterface
{
    /** @var Expense $entity */
    public function extract(BalanceEntityInterface $entity): array
    {
        return [
            'id' => $entity->getId(),
            'amount' => $entity->getAmount(),
            'category' => $entity->getCategory()->getName(),
            'created_timestamp' => $entity->getCreated()->getTimestamp()
        ];
    }

    public function hydrate(array $data, EntityManagerInterface $entityManager): BalanceEntityInterface
    {
        return (new Expense())
            ->setAmount($data['amount'])
            ->setCategory($entityManager->find(ExpenseCategory::class, $data['category']));
    }
}