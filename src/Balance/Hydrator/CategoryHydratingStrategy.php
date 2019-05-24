<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\ExpenseCategory;

final class CategoryHydratingStrategy implements HydratingStrategyInterface
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

    public function hydrate(array $data, ?BalanceEntityInterface $entity): BalanceEntityInterface
    {
        if (!$entity) {
            return (new ExpenseCategory($data['name'], $data['author']))
                ->changeDescription($data['description']);
        }

        /** @var ExpenseCategory $entity */
        return $entity
            ->changeName($data['name'])
            ->changeDescription($data['description']);
    }
}
