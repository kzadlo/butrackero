<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;

interface HydrationStrategyInterface
{
    public function extract(BalanceEntityInterface $entity): array;
    public function hydrate(array $data): BalanceEntityInterface;
}