<?php

namespace App\Balance\Hydrator;

use App\Balance\Model\BalanceEntityInterface;
use Doctrine\ORM\EntityManagerInterface;

class BalanceHydrator
{
    public function extract(BalanceEntityInterface $entity, HydrationStrategyInterface $strategy): array
    {
        return $strategy->extract($entity);
    }

    public function hydrate(
        array $data,
        HydrationStrategyInterface $strategy,
        EntityManagerInterface $entityManager
    ): BalanceEntityInterface {
        return $strategy->hydrate($data, $entityManager);
    }

    public function extractSeveral(array $entities, HydrationStrategyInterface $strategy): array
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $this->extract($entity, $strategy);
        }

        return $data;
    }
}