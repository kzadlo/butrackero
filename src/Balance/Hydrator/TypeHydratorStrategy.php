<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\IncomeType;

class TypeHydratorStrategy implements HydrationStrategyInterface
{
    /** @var IncomeType $entity */
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
        return (new IncomeType())
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setAuthor($data['author']);
    }
}
