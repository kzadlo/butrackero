<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\ExpenseCategory;

class CategoryHydratorStrategy implements HydrationStrategyInterface
{
    /** @var ExpenseCategory $entity */
    public function extract(BalanceEntityInterface $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription()
        ];
    }

    public function hydrate(array $data): BalanceEntityInterface
    {
        return (new ExpenseCategory($data['name'], $data['author']))
            ->changeDescription($data['description']);
    }
}
