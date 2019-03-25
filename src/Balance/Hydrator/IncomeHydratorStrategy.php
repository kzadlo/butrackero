<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;

class IncomeHydratorStrategy implements HydrationStrategyInterface
{
    /** @var Income $entity */
    public function extract(BalanceEntityInterface $entity): array
    {
        return [
            'id' => $entity->getId(),
            'amount' => $entity->getAmount(),
            'category' => $entity->getType()->getName(),
            'createdTimestamp' => $entity->getCreated()->getTimestamp()
        ];
    }

    public function hydrate(array $data): BalanceEntityInterface
    {
        return (new Income())
            ->setAmount($data['amount'])
            ->setType($data['type'])
            ->setAuthor($data['author']);
    }
}
