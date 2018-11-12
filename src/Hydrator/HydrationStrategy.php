<?php

namespace App\Hydrator;

use App\Entity\BalanceEntityInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class HydrationStrategy
{
    public abstract function extract(BalanceEntityInterface $entity): array;
    public abstract function hydrate(array $data, EntityManagerInterface $entityManager): BalanceEntityInterface;

    public function extractSeveral(array $entities): array
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $this->extract($entity);
        }

        return $data;
    }
}