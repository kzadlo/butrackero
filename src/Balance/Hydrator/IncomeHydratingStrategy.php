<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use App\Balance\Model\Income;

final class IncomeHydratingStrategy implements HydratingStrategyInterface
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

    public function hydrate(array $data, ?BalanceEntityInterface $entity): BalanceEntityInterface
    {
        if (!$entity) {
            return new Income($data['amount'], $data['type'], $data['author']);
        }

        /** @var Income $entity */
        return $entity
            ->changeAmount($data['amount'])
            ->changeType($data['type']);
    }
}
