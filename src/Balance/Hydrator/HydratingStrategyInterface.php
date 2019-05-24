<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;

interface HydratingStrategyInterface
{
    public function extract(BalanceEntityInterface $entity): array;

    public function hydrate(array $data, ?BalanceEntityInterface $entity): BalanceEntityInterface;
}
