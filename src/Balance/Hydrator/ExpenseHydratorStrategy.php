<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Expense;

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

    public function hydrate(array $data): BalanceEntityInterface
    {
        return (new Expense())
            ->setAmount($data['amount'])
            ->setCategory($data['category'])
            ->setAuthor($data['author']);
    }
}