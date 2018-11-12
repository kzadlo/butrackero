<?php

namespace App\Hydrator;

use App\Entity\BalanceEntityInterface;
use Doctrine\ORM\EntityManagerInterface;

class BalanceHydrator
{
    public function extract(BalanceEntityInterface $entity, HydrationStrategy $strategy): array
    {
        return $strategy->extract($entity);
    }

    public function hydrate(
        array $data,
        HydrationStrategy $strategy,
        EntityManagerInterface $entityManager
    ): BalanceEntityInterface {
        return $strategy->hydrate($data, $entityManager);
    }
}